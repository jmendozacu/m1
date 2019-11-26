<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Helper_Ups extends Mage_Core_Helper_Abstract
{
    public
    function setParams($lbl, $method, $request)
    {
        

        $packages = array();
        $packages[0]['weight'] = $request->getPackageWeight();
        $packages[0]['large'] = $request->getPackageWeight() > 90 ? '<LargePackageIndicator />' : '';

        $lbl->shiptoStateProvinceCode = Infomodus_Caship_Helper_Data::escapeXML($request->getDestRegionCode());
        $lbl->shiptoCity = $request->getDestCity();
        $lbl->shiptoPostalCode = $request->getDestPostcode();
        $lbl->shiptoCountryCode = $request->getDestCountryId();

        if ($method->company_type == 'upsinfomodus'/*Mage::helper('core')->isModuleOutputEnabled("Infomodus_Upslabel")*/) {

            $packages = $this->intermediateHandy($request);

            $lbl->AccessLicenseNumber = Mage::getStoreConfig('upslabelinv/profile/accesslicensenumber');
            $lbl->UserId = Mage::getStoreConfig('upslabelinv/profile/userid');
            $lbl->Password = Mage::getStoreConfig('upslabelinv/profile/password');
            $lbl->shipperNumber = Mage::getStoreConfig('upslabelinv/profile/shippernumber');

            $lbl->shipperCity = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipper/city'));
            $lbl->shipperStateProvinceCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipper/stateprovincecode'));
            $lbl->shipperPostalCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipper/postalcode'));
            $lbl->shipperCountryCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipper/countrycode'));


            $lbl->shipfromStateProvinceCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipfrom/stateprovincecode'));
            $lbl->shipfromPostalCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipfrom/postalcode'));
            $lbl->shipfromCountryCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('upslabelinv/shipfrom/countrycode'));

            $lbl->weightUnits = "LBS";

            $lbl->testing = Mage::getStoreConfig('upslabelinv/profile/testing');
        } else {
            $typeCodes = array(
                'CP' => '00', // Customer Packaging
                'ULE' => '01', // UPS Letter Envelope
                'CSP' => '02', // Customer Supplied Package
                'UT' => '03', // UPS Tube
                'PAK' => '04', // PAK
                'UEB' => '21', // UPS Express Box
                'UW25' => '24', // UPS Worldwide 25 kilo
                'UW10' => '25', // UPS Worldwide 10 kilo
                'PLT' => '30', // Pallet
                'SEB' => '2a', // Small Express Box
                'MEB' => '2b', // Medium Express Box
                'LEB' => '2c', // Large Express Box
            );
            $packages[0]['packagingtypecode'] = $typeCodes[Mage::getStoreConfig('carriers/ups/container')];
            $packages[0]['packweight'] = 0;
            $packages[0]['additionalhandling'] = strlen(Mage::getStoreConfig('carriers/ups/handling_fee')) > 0 && Mage::getStoreConfig('carriers/ups/handling_fee') > 0 ? '<AdditionalHandling />' : '';

            $lbl->AccessLicenseNumber = Mage::getStoreConfig('carriers/ups/access_license_number');
            $lbl->UserId = Mage::getStoreConfig('carriers/ups/username');
            $lbl->Password = Mage::getStoreConfig('carriers/ups/password');
            $lbl->shipperNumber = Mage::getStoreConfig('carriers/ups/shipper_number');

            $lbl->shipperCity = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('shipping/origin/city'));
            $region = Mage::getStoreConfig('shipping/origin/region_id');
            if(is_numeric($region)){
                $region = Mage::getModel('directory/region')->load($region);
                $region = $region->getCode();
            }
            $lbl->shipperStateProvinceCode = Infomodus_Caship_Helper_Data::escapeXML($region);
            $lbl->shipperPostalCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('shipping/origin/postcode'));
            $lbl->shipperCountryCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('shipping/origin/country_id'));

            $lbl->shipfromStateProvinceCode = Infomodus_Caship_Helper_Data::escapeXML($region);
            $lbl->shipfromPostalCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('shipping/origin/postcode'));
            $lbl->shipfromCountryCode = Infomodus_Caship_Helper_Data::escapeXML(Mage::getStoreConfig('shipping/origin/country_id'));

            $lbl->weightUnits = Mage::getStoreConfig('carriers/ups/unit_of_measure');
            $lbl->includeDimensions = 0;
            $lbl->testing = Mage::getStoreConfig('carriers/ups/mode_xml') == 1 ? 0 : 1;
        }

        $lbl->packages = $packages;

        $lbl->negotiated_rates = $method->getDinamicPrice() == 1 ? $method->getNegotiated() : 0;
        $lbl->rates_tax = $method->getTax();
        if (Mage::getSingleton('checkout/session')->getQuote()->getSubtotal() < $method->getNegotiatedAmountFrom()) {
            $lbl->negotiated_rates = 0;
        }
        return $lbl;
    }

    private function intermediateHandy(Mage_Shipping_Model_Rate_Request $request)
    {
        $countProductInBox = 0;
        $i = 0;
        $packages = array();

        if ($countProductInBox == 0) {
            $packages = array();
            $packages[0] = $this->setDefParams(array('weight' => $request->getPackageWeight()));
        }
        return $packages;
    }

    private function setDefParams($itemData)
    {
        $packages = array();
        $packages['weight'] = $itemData['weight'];
        $packages['large'] = $itemData['weight'] > 90 ? '<LargePackageIndicator />' : '';
        $packages['packagingtypecode'] = Mage::getStoreConfig('upslabelinv/profile/packagingtypecode');
        $packages['packweight'] = '0';
        $packages['additionalhandling'] = '';
        $packages['insuredmonetaryvalue'] = 0;
        return $packages;
    }
}