<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Model_Ups
{

    public $AccessLicenseNumber;
    public $UserId;
    public $Password;
    public $shipperNumber;

    public $packages;
    public $weightUnits;
    public $packageWeight;
    public $weightUnitsDescription;
    public $largePackageIndicator;
    public $dimentionUnitKoef = 1;
    public $weightUnitKoef = 1;

    public $shipperCity;
    public $shipperStateProvinceCode;
    public $shipperPostalCode;
    public $shipperCountryCode;
    public $shipmentDescription;
    public $shipperAttentionName;

    public $shiptoCity;
    public $shiptoStateProvinceCode;
    public $shiptoPostalCode;
    public $shiptoCountryCode;

    public $shipfromCity;
    public $shipfromStateProvinceCode;
    public $shipfromPostalCode;
    public $shipfromCountryCode;

    public $testing;

    public $adult = 0;


    

    public $negotiated_rates;

    public function timeInTransit($weightSum = 0.1)
    {
        /*$cie = 'wwwcie';
        $testing = $this->testing;
        if (0 == $testing) {*/
            $cie = 'onlinetools';
        /*}*/
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserId . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<TimeInTransitRequest xml:lang='en-US'>
<Request>
<TransactionReference>
<CustomerContext>Shipper</CustomerContext>
<XpciVersion>1.0002</XpciVersion>
</TransactionReference>
<RequestAction>TimeInTransit</RequestAction>
</Request>
<TransitFrom>
<AddressArtifactFormat>
<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
<PostcodePrimaryLow>" . $this->shipfromPostalCode . "</PostcodePrimaryLow>
</AddressArtifactFormat>
</TransitFrom>
<TransitTo>
<AddressArtifactFormat>
<PoliticalDivision2>" . $this->shiptoCity . "</PoliticalDivision2>
<PoliticalDivision1>" . $this->shiptoStateProvinceCode . "</PoliticalDivision1>
<CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
<PostcodePrimaryLow>" . $this->shiptoPostalCode . "</PostcodePrimaryLow>
</AddressArtifactFormat>
</TransitTo>
<ShipmentWeight>
<UnitOfMeasurement>
<Code>" . $this->weightUnits . "</Code>
</UnitOfMeasurement>
<Weight>" . $weightSum . "</Weight>
</ShipmentWeight>
<PickupDate>" . date('Ymd') . "</PickupDate>
<DocumentsOnlyIndicator />
</TimeInTransitRequest>";
        $curl = Mage::helper('caship');
        $curl->testing = true;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/TimeInTransit', $data);
        if (Mage::getStoreConfig('carriers/caship/debug') == 1) {
            Mage::log($data, null, 'caship_debug.log');
            Mage::log($result, null, 'caship_debug.log');
        }
        if (!$curl->error) {
            $xml = $this->xml2array(simplexml_load_string($result));
            if ($xml['Response']['ResponseStatusCode'] == 0 || $xml['Response']['ResponseStatusDescription'] != 'Success') {
                return array('error' => 1);
            } else {
                $countDay = array();
                if(isset($xml['TransitResponse'])) {
                    foreach ($xml['TransitResponse']['ServiceSummary'] AS $v) {
                        $codes = $curl->getUpsCode($v['Service']['Code']);
                        if (!is_array($codes)) {
                            $codes = array($codes);
                        }
                        if (isset($v['EstimatedArrival']['TotalTransitDays'])) {
                            foreach ($codes AS $v2) {
                                $countDay[$v2]['days'] = $v['EstimatedArrival']['TotalTransitDays'];
                                $countDay[$v2]['datetime']['date'] = $v['EstimatedArrival']['Date'];
                                $countDay[$v2]['datetime']['time'] = $v['EstimatedArrival']['Time'];
                            }

                        } else if (isset($v['EstimatedArrival']['BusinessTransitDays'])) {
                            foreach ($codes AS $v2) {
                                $countDay[$v2]['days'] = $v['EstimatedArrival']['BusinessTransitDays'];
                                $countDay[$v2]['datetime']['date'] = $v['EstimatedArrival']['Date'];
                                $countDay[$v2]['datetime']['time'] = $v['EstimatedArrival']['Time'];
                            }
                        }
                    }
                }
                return array('error' => 0, 'days' => $countDay);
            }
        } else {
            return $result;
        }
    }

    public function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? $this->xml2array($node) : (is_array($node)) ? $this->xml2array($node) : $node;
        }
        return $out;
    }

    function getShipRate()
    {
        $weightSum = 0;
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserId . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<RatingServiceSelectionRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>Rating and Service</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>Shop</RequestOption>
  </Request>
  <PickupType>
          <Code>03</Code>
          <Description>Customer Counter</Description>
  </PickupType>
  <Shipment>";
        if ($this->negotiated_rates == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        $data .= "<Shipper>";
        $data .= "<ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
      <Address>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>
	<ShipTo>
      <Address>
        <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
        <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
        <ResidentialAddress>02</ResidentialAddress>
      </Address>
    </ShipTo>
    <ShipFrom>
      <Address>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>";
        foreach ($this->packages AS $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>";
            $data .= array_key_exists('additionalhandling', $pv) ? $pv['additionalhandling'] : '';
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? $pv['weight'] : '';
            $weightSum += $weight;
            $data .= "</UnitOfMeasurement>
        <Weight>" . round(($weight*$this->weightUnitKoef + (is_numeric($packweight = str_replace(',', '.', $packweight)) ? $packweight*$this->weightUnitKoef : 0)), 1) . "</Weight>" . (array_key_exists('large', $pv) ? $pv['large'] : '') . "
      </PackageWeight>";
            if ($this->isAdult('P')) {
                $data .= "<PackageServiceOptions>";
                $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
                $data .= "</PackageServiceOptions>";
            }
              $data .= "</Package>";
        }
        if ($this->isAdult('S')) {
            $data .= "<ShipmentServiceOptions>";
            $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
            $data .= "<.ShipmentServiceOptions>";
        }
        $data .= "</Shipment></RatingServiceSelectionRequest>";
        /*$cie = 'wwwcie';
        if (0 == $this->testing) {*/
            $cie = 'onlinetools';
        /*}*/


        $curl = Mage::helper('caship');
        $curl->testing = true;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/Rate', $data);

        if (Mage::getStoreConfig('carriers/caship/debug') == 1) {
            Mage::log($data, null, 'caship_debug.log');
            Mage::log($result, null, 'caship_debug.log');
        }

        if ($curl->error === false) {
            $result = strstr($result, '<?xml');
            //return $data;
            $xml = simplexml_load_string($result);
            if ($xml->Response->ResponseStatusCode[0] == 1 || $xml->Response->ResponseStatusCode == 1) {
                $rates = array();
                $timeInTransit = null;
                if (Mage::getStoreConfig('carriers/caship/debug') == 1) {
                    Mage::log($xml, null, 'caship_debug.log');
                }
                foreach ($xml->RatedShipment AS $rated) {
                    if (is_array($rated->Service) && count($rated->Service) > 0) {
                        if (is_array($rated->Service[0]->Code) && count($rated->Service[0]->Code) > 0) {
                            $rateCode = (string)$rated->Service[0]->Code[0];
                        } else {
                            $rateCode = (string)$rated->Service[0]->Code;
                        }
                    } else {
                        if (is_array($rated->Service->Code) && count($rated->Service->Code) > 0) {
                            $rateCode = (string)$rated->Service->Code[0];
                        } else {
                            $rateCode = (string)$rated->Service->Code;
                        }
                    }
                    /*$time = (string)$rated->GuaranteedDaysToDelivery;*/
                    /*if ($rated->Service[0]->Code[0] == $this->serviceCode) {*/
                    $defaultCurrency = $rated->TotalCharges[0]->CurrencyCode;
                    $defaultPrice = $rated->TotalCharges[0]->MonetaryValue;
                    if (!$rated->NegotiatedRates) {
                        $rates[$rateCode] = array(
                            'price' => $defaultPrice,
                            'currency' => $defaultCurrency,
                        );
                    } else {
                        $defaultPrice = $rated->NegotiatedRates;
                        if (isset($defaultPrice[0])) {
                            $defaultPrice = $defaultPrice[0];
                        }
                        $defaultPrice = $defaultPrice->NetSummaryCharges;
                        if (isset($defaultPrice[0])) {
                            $defaultPrice = $defaultPrice[0];
                        }
                        $defaultPrice = $defaultPrice->GrandTotal;
                        if (isset($defaultPrice[0])) {
                            $defaultPrice = $defaultPrice[0];
                        }
                        $defaultCurrency = $defaultPrice->CurrencyCode;
                        $defaultPrice = $defaultPrice->MonetaryValue;
                        if ($this->rates_tax == 1) {
                            $defaultPrice2 = $rated->NegotiatedRates;
                            if (isset($defaultPrice2[0])) {
                                $defaultPrice2 = $defaultPrice2[0];
                            }
                            $defaultPrice2 = $defaultPrice2->NetSummaryCharges;
                            if (isset($defaultPrice2[0])) {
                                $defaultPrice2 = $defaultPrice2[0];
                            }
                            $defaultPrice2 = $defaultPrice2->TotalChargesWithTaxes;
                            if (isset($defaultPrice2[0])) {
                                $defaultPrice2 = $defaultPrice2[0];
                            }
                            $defaultCurrency2 = $defaultPrice2->CurrencyCode;
                            $defaultPrice2 = $defaultPrice2->MonetaryValue;
                            if ($defaultPrice2) {
                                $defaultPrice = $defaultPrice2;
                                $defaultCurrency = $defaultCurrency2;
                            }
                        }
                        $rates[$rateCode] = array(
                            'price' => $defaultPrice,
                            'currency' => $defaultCurrency,
                        );
                    }
                    /*}*/
                    if ($timeInTransit === null) {
                        $timeInTransit = $this->timeInTransit($weightSum);
                    }
                    if (is_array($timeInTransit) && isset($timeInTransit['days'][$rateCode])) {
                        $rates[$rateCode]['day'] = $timeInTransit['days'][$rateCode];
                    }
                }
                return $rates;
            } else {
                $error = array('error' => $xml->Response[0]->Error[0]->ErrorDescription[0]);
                $errorLog = Mage::getModel("caship/errorlog");
                $errorLog->setErrorMessage($error['error'])->save();
                return $error;
            }
        } else {
            $error = array('error' => $result["errordesc"]);
            $errorLog = Mage::getModel("caship/errorlog");
            $errorLog->setErrorMessage($error['error'])->save();
            return $error;
        }
    }

    protected function isAdult($typeService)
    {
        if ($this->adult == 4) {
            if ($typeService === "P") {
                return false;
            } else if ($typeService === "S") {
                return true;
            }
        }
        if ($typeService === "S") {
            $this->adult = $this->adult - 1;
        }
        if ($this->adult == 0) {
            return false;
        }

        $adult = 'DC';
        if ($typeService === 'P') {
            if ($this->adult == 2) {
                $adult = 'DC-SR';
            } else if ($this->adult == 3) {
                $adult = 'DC-ASR';
            }
        } else if ($typeService === 'S') {
            if ($this->adult == 1) {
                $adult = 'DC-SR';
            } else if ($this->adult == 2) {
                $adult = 'DC-ASR';
            }
        }

        switch ($this->shipfromCountryCode) {
            case 'US':
            case 'CA':
            case 'PR':
                switch ($this->shiptoCountryCode) {
                    case 'US':
                    case 'PR':
                        if ($typeService === 'P') {
                            return true;
                        }
                        break;
                    default:
                        if ($typeService === 'S' && ($adult === 'DC-SR' || $adult === 'DC-ASR')) {
                            return true;
                        }
                        break;
                }
                break;
            default:
                if ($typeService === 'S' && ($adult === 'DC-SR' || $adult === 'DC-ASR')) {
                    return true;
                }
                break;
        }

        return false;
    }
}