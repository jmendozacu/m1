<?php

/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Label_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    private $_order_id = false;
    private $_direction = false;
    private $_without = false;
    private $_throwExceptions = false;

    protected function _beforeToHtml()
    {
        $AccessLicenseNumber = Mage::getStoreConfig('upslabelinv/profile/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabelinv/profile/userid');
        $Password = Mage::getStoreConfig('upslabelinv/profile/password');
        $shipperNumber = Mage::getStoreConfig('upslabelinv/profile/shippernumber');

        $order_id = $this->_order_id !== false ? $this->_order_id : $this->getRequest()->getParam('order_id');
        $direction = $this->_direction !== false ? $this->_direction : $this->getRequest()->getParam('direction');
        $newsss = $this->getRequest()->getParam('new');
        $ship_id = $this->getRequest()->getParam('ship_id');

        $path = Mage::getBaseDir('media') . DS . 'upslabelinv' . DS . 'label' . DS;

        $lbl = new Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Ups();

        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);


        $collection1 = Mage::getModel('upslabelinv/upslabelinv')->getCollection()->addFieldToFilter('type', $direction)->addFieldToFilter('order_id', $order_id);
        if ($collection1->getSize() > 0) {
            $collection = $collection1->getData();
            $collection = $collection[0];
        }
        if ($collection1->getSize() == 0) {
            $order = Mage::getModel('sales/order')->load($order_id);
            //$customer = Mage::getModel('customer/customer')->load($order['customer_id']);
            //$shiping_adress = Mage::getModel('sales/order_address')->load($order['shipping_address_id']);
            $shiping_adress = $order->getShippingAddress();
            //$quote = Mage::getModel('sales/quote');
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            /* echo '<ul>';
              foreach (get_class_methods(get_class($quote)) as $cMethod) {
              echo '<li>' . $cMethod . '</li>';
              }
              echo '</ul>'; */
            //echo $order->getShippingCarrier();
            //print_r($order->getShippingAddress());
            $onlyups = Mage::getStoreConfig('upslabelinv/profile/onlyups');
            if ($shipByUps == 'ups' || $onlyups == 0) {
                $provinceCode = array(
                    'Alabama' => 'AL',
                    'Alaska' => 'AK',
                    'American Samoa' => 'AS',
                    'Arizona' => 'AZ',
                    'Arkansas' => 'AR',
                    'Armed Forces Africa' => 'AE',
                    'Armed Forces Americas' => 'AA',
                    'Armed Forces Canada' => 'AE',
                    'Armed Forces Europe' => 'AE',
                    'Armed Forces Middle East' => 'AE',
                    'Armed Forces Pacific' => 'AP',
                    'California' => 'CA',
                    'Colorado' => 'CO',
                    'Connecticut' => 'CT',
                    'Delaware' => 'DE',
                    'District of Columbia' => 'DC',
                    'Federated States Of Micronesia' => 'FM',
                    'Florida' => 'FL',
                    'Georgia' => 'GA',
                    'Guam' => 'GU',
                    'Hawaii' => 'HI',
                    'Idaho' => 'ID',
                    'Illinois' => 'IL',
                    'Indiana' => 'IN',
                    'Iowa' => 'IA',
                    'Kansas' => 'KS',
                    'Kentucky' => 'KY',
                    'Louisiana' => 'LA',
                    'Maine' => 'ME',
                    'Marshall Islands' => 'MH',
                    'Maryland' => 'MD',
                    'Massachusetts' => 'MA',
                    'Michigan' => 'MI',
                    'Minnesota' => 'MN',
                    'Mississippi' => 'MS',
                    'Missouri' => 'MO',
                    'Montana' => 'MT',
                    'Nebraska' => 'NE',
                    'Nevada' => 'NV',
                    'New Hampshire' => 'NH',
                    'New Jersey' => 'NJ',
                    'New Mexico' => 'NM',
                    'New York' => 'NY',
                    'North Carolina' => 'NC',
                    'North Dakota' => 'ND',
                    'Northern Mariana Islands' => 'MP',
                    'Ohio' => 'OH',
                    'Oklahoma' => 'OK',
                    'Oregon' => 'OR',
                    'Palau' => 'PW',
                    'Pennsylvania' => 'PA',
                    'Puerto Rico' => 'PR',
                    'Rhode Island' => 'RI',
                    'South Carolina' => 'SC',
                    'South Dakota' => 'SD',
                    'Tennessee' => 'TN',
                    'Texas' => 'TX',
                    'Utah' => 'UT',
                    'Vermont' => 'VT',
                    'Virgin Islands' => 'VI',
                    'Virginia' => 'VA',
                    'Washington' => 'WA',
                    'West Virginia' => 'WV',
                    'Wisconsin' => 'WI',
                    'Wyoming' => 'WY',
                    /* Canada */
                    'Alberta' => 'AB',
                    'British Columbia' => 'BC',
                    'Manitoba' => 'MB',
                    'New Brunswick' => 'NB',
                    'Newfoundland and Labrador' => 'NL',
                    'Northwest Territories' => 'NT',
                    'Nova Scotia' => 'NS',
                    'Nunavut' => 'NU',
                    'Ontario' => 'ON',
                    'Prince Edward Island' => 'PE',
                    'Quebec' => 'QC',
                    'Saskatchewan' => 'SK',
                    'Yukon' => 'YT',
                    '' => '',
                    '' => '',
                );
                $sercoD = array(
                    '1DM' => '14',
                    '1DA' => '01',
                    '1DP' => '13',
                    '2DM' => '59',
                    '2DA' => '02',
                    '3DS' => '12',
                    'GND' => '03',
                    'EP' => '54',
                    'ES' => '07',
                    'SV' => '65',
                    'EX' => '08',
                    'ST' => '11',
                    'ND' => '07',
                );

                $sercoD2 = array(
                    '14' => '14',
                    '1' => '01',
                    '13' => '13',
                    '59' => '59',
                    '2' => '02',
                    '12' => '12',
                    '3' => '03',
                    '54' => '54',
                    '7' => '07',
                    '65' => '65',
                    '8' => '08',
                    '11' => '11',
                    '7' => '07',
                );

                $shipMethodArray = explode('_', $ship_method);

                $shipByUpsCode = preg_replace("/^ups_(.{2,4})$/", '$1', $ship_method);
                $db = Mage::getSingleton('core/resource')->getConnection('core_write');
                $shipByUpsName = $db->query('SELECT method_title FROM ' . Mage::app()->getConfig()->getNode('global/resources/db')->table_prefix . 'sales_flat_quote_shipping_rate where code=\'' . $ship_method . '\'')->fetch();
                $lbl->shipmentDescription = $this->getRequest()->getParam('description') ? $this->getRequest()->getParam('description') : Mage::getStoreConfig('upslabelinv/profile/description');
                $lbl->shipperName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipper/name'));
                $lbl->shipperAttentionName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipper/attentionname'));
                $lbl->shipperPhoneNumber = Mage::getStoreConfig('upslabelinv/shipper/phonenumber');
                $lbl->shipperAddressLine1 = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipper/addressline1'));
                $lbl->shipperCity = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipper/city'));
                $lbl->shipperStateProvinceCode = Mage::getStoreConfig('upslabelinv/shipper/stateprovincecode');
                $lbl->shipperPostalCode = Mage::getStoreConfig('upslabelinv/shipper/postalcode');
                $lbl->shipperCountryCode = Mage::getStoreConfig('upslabelinv/shipper/countrycode');

                $lbl->shiptoCompanyName = strlen($shiping_adress['company']) == 0 ? Mage::getStoreConfig('upslabelinv/profile/companyname') : $shiping_adress['company'];
                $lbl->shiptoCompanyName = str_replace('&', '&amp;', strlen($lbl->shiptoCompanyName) == 0 ? $shiping_adress['firstname'] . ' ' . $shiping_adress['lastname'] : $lbl->shiptoCompanyName);
                $lbl->shiptoAttentionName = str_replace('&', '&amp;', $shiping_adress['firstname'] . ' ' . $shiping_adress['lastname']);
                $lbl->shiptoPhoneNumber = substr($shiping_adress['telephone'], 0, 15);
                $lbl->shiptoAddressLine1 = str_replace('&', '&amp;', $shiping_adress['street']);
                $lbl->shiptoCity = str_replace('&', '&amp;', $shiping_adress['city']);
                $lbl->shiptoStateProvinceCode = array_key_exists($shiping_adress['region'], $provinceCode) ? $provinceCode[$shiping_adress['region']] : '';
                $lbl->shiptoPostalCode = $shiping_adress['postcode'];
                $lbl->shiptoCountryCode = $shiping_adress['country_id'];
                $lbl->shiptoCustomerEmail = $shiping_adress['email'];

                $lbl->shipfromCompanyName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipfrom/companyname'));
                $lbl->shipfromAttentionName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipfrom/attentionname'));
                $lbl->shipfromPhoneNumber = Mage::getStoreConfig('upslabelinv/shipfrom/phonenumber');
                $lbl->shipfromAddressLine1 = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipfrom/addressline1'));
                $lbl->shipfromCity = Mage::getStoreConfig('upslabelinv/shipfrom/city');
                $lbl->shipfromStateProvinceCode = Mage::getStoreConfig('upslabelinv/shipfrom/stateprovincecode');
                $lbl->shipfromPostalCode = Mage::getStoreConfig('upslabelinv/shipfrom/postalcode');
                $lbl->shipfromCountryCode = Mage::getStoreConfig('upslabelinv/shipfrom/countrycode');

                $lbl->shipto2CompanyName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipto/companyname'));
                $lbl->shipto2AttentionName = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipto/attentionname'));
                $lbl->shipto2PhoneNumber = Mage::getStoreConfig('upslabelinv/shipto/phonenumber');
                $lbl->shipto2AddressLine1 = str_replace('&', '&amp;', Mage::getStoreConfig('upslabelinv/shipto/addressline1'));
                $lbl->shipto2City = Mage::getStoreConfig('upslabelinv/shipto/city');
                $lbl->shipto2StateProvinceCode = Mage::getStoreConfig('upslabelinv/shipto/stateprovincecode');
                $lbl->shipto2PostalCode = Mage::getStoreConfig('upslabelinv/shipto/postalcode');
                $lbl->shipto2CountryCode = Mage::getStoreConfig('upslabelinv/shipto/countrycode');

                if ($shipMethodArray[0] == 'caship') {
                    $upsmethodId = $shipMethodArray[1];
                    $cashipModel = Mage::getModel('caship/method')->load($upsmethodId);
                    if ($cashipModel) {
                        switch ($cashipModel->getDirectionType()) {
                            case 1:
                                $toCode = $cashipModel->getUpsmethodId();
                                $fromCode = $cashipModel->getUpsmethodId();
                                $to2Code = $cashipModel->getUpsmethodId();
                                break;
                            case 2:
                                $toCode = $cashipModel->getUpsmethodId();
                                $fromCode = $cashipModel->getUpsmethodId_2();
                                $to2Code = $cashipModel->getUpsmethodId_3();
                                break;
                            case 3:
                                $toCode = $cashipModel->getUpsmethodId();
                                $fromCode = $cashipModel->getUpsmethodId_2();
                                $to2Code = $cashipModel->getUpsmethodId_3();
                                break;
                        }
                        switch ($direction) {
                            case 'to':
                                $lbl->serviceCode = $toCode;
                                break;
                            case 'from':
                                $lbl->serviceCode = $fromCode;
                                break;
                            case 'to2':
                                $lbl->serviceCode = $to2Code;
                                break;
                        }
                    }
                } elseif ($shipMethodArray[0] == 'upstablerates') {
                    $upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates_collection');
                    $modeltableratesData = $upstablerates->getData();
                    foreach ($modeltableratesData AS $v) {
                        if ($shipMethodArray[2] == $v['pk']) {
                            $toCode = 3;
                            $fromCode = 3;
                            $to2Code = 3;
                            $Code = explode('-', $v['method_code']);
                            switch (count($Code)) {
                                case 1:
                                    $toCode = $Code[0];
                                    $fromCode = $Code[0];
                                    $to2Code = $Code[0];
                                    break;
                                case 3:
                                    $toCode = $Code[0];
                                    $fromCode = $Code[1];
                                    $to2Code = $Code[2];
                                    break;
                                case 2:
                                    $shipMethodArray = explode('_', $order->getShippingMethod());
                                    $shipWay = 0;
                                    if ($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) {
                                        $upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->loadPk($shipMethodArray[2]);
                                        $shipWay = $upstablerates['way'];
                                    }
                                    if ((int)$shipWay == 3) {
                                        $toCode = $Code[0];
                                        $fromCode = $Code[1];
                                        $to2Code = $Code[1];
                                    } else {
                                        $toCode = $Code[0];
                                        $fromCode = $Code[1];
                                        $to2Code = $Code[0];
                                    }
                                    break;
                            }
                            switch ($direction) {
                                case 'to':
                                    $lbl->serviceCode = $toCode;
                                    break;
                                case 'from':
                                    $lbl->serviceCode = $fromCode;
                                    break;
                                case 'to2':
                                    $lbl->serviceCode = $to2Code;
                                    break;
                            }
                        }
                    }
                    //var_dump($modeltablerates->getData()); exit;
                } else if ($this->getRequest()->getParam('intermediate')) {
                    $lbl->serviceCode = $sercoD[$this->getRequest()->getParam('intermediate')];
                } else {
                    $lbl->serviceCode = array_key_exists($shipByUpsCode, $sercoD) ? $sercoD[$shipByUpsCode] : $shipByUpsCode;
                }
                // echo '//////////////// '.$lbl->serviceCode. ' ////////////////////'; exit;
                if(strlen($lbl->serviceCode)==1) {
                    $lbl->serviceCode = $sercoD2[(int)$lbl->serviceCode];
                }
                if (strlen(trim($lbl->serviceCode)) == 0) {
                    $lbl->serviceCode = '03';
                }
                /*echo $lbl->serviceCode; exit;*/

                $lbl->serviceDescription = $shipByUpsName['method_title'];

                $lbl->packageWeight = $order['weight'];

                $lbl->packagingTypeCode = Mage::getStoreConfig('upslabelinv/profile/packagingtypecode');
                $lbl->packagingDescription = Mage::getStoreConfig('upslabelinv/profile/packagingdescription');
                $lbl->packagingReferenceNumberCode = Mage::getStoreConfig('upslabelinv/profile/packagingreferencenumbercode');
                $lbl->packagingReferenceNumberValue = Mage::getStoreConfig('upslabelinv/profile/packagingreferencenumbervalue');
                $lbl->orderPrice = $order->getGrandTotal();

                if ($direction == 'to' || $direction == 'to2') {
                    $upsl = $lbl->getShipTo($order_id);
                } else if ($direction == 'from') {
                    $upsl = $lbl->getShipFrom($order_id);
                }
                if (!array_key_exists('error', $upsl)) {
                    $upslabel = Mage::getModel('upslabelinv/upslabelinv');
                    $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl['trackingnumber']);
                    $upslabel->setOrderId($order_id);
                    $upslabel->setTrackingnumber($upsl['trackingnumber']);
                    $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                    $upslabel->setShipmentdigest($upsl['digest']);
                    $upslabel->setLabelname($upsl['img_name']);
                    $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                    $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                    $upslabel->setType($direction);
                    $upslabel->save();

                    $shipMethodArray = explode('_', $order->getShippingMethod());
                    $shipWay = 0;
                    if ($shipMethodArray[0] == 'caship') {
                        $upsmethodId = $shipMethodArray[1];
                        $cashipModel = Mage::getModel('caship/method')->load($upsmethodId);
                        if ($cashipModel) {
                            $shipWay = $cashipModel->getDirectionType();
                        }
                    }
                    if ($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) {
                        $upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->loadPk($shipMethodArray[2]);
                        $shipWay = $upstablerates['way'];
                    }
                    if ($direction == 'from' && (int)$shipWay != 3 && (int)$shipWay != 1 && (Mage::getStoreConfig('upslabelinv/way/emailusend') == 1 || ($this->_direction === false && Mage::getStoreConfig('upslabelinv/way/emailusend') == 0))) {
                        $customer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->load($order->getCustomerId());
                        $customerEmail = $customer->getEmail();
                        $customerFirstname = $customer->getFirstname();
                        $customerLastname = $customer->getLastname();

                        $mail = new Zend_Mail();
                        $mail->setType(Zend_Mime::MULTIPART_RELATED);
                        $mail->setBodyHtml('UPS Shippinl Label is attached to this message.<br />If you don\'t see this image, please ckick on the link: <a href="' . $this->getUrl('upslabelinv/label/view/order_id/' . $order_id) . '">' . $this->getUrl('upslabelinv/label/view/order_id/' . $order_id) . '</a>');
                        $fileGif = $mail->createAttachment(file_get_contents(Mage::getBaseUrl('media') . 'upslabelinv/label/' . $upsl['img_name']),
                            'image/gif',
                            Zend_Mime::DISPOSITION_INLINE,
                            Zend_Mime::ENCODING_BASE64);
                        $fileGif->filename = 'label.gif';
                        $mail->setFrom(Mage::getStoreConfig('upslabelinv/way/emailusersend'), Mage::app()->getStore()->getName());

                        $mail->addTo($customerEmail, $customerFirstname . ' ' . $customerLastname);

                        $mail->setSubject('Ups label for order number ' . $order->getIncrementId());
                        $mail->send();
                    }

                    if ($this->_without === false) {
                        echo '<h1> Order ID ' . $order_id . ' TN ' . $upsl['trackingnumber'] . '</h1>
<br />
<a href="' . $this->getUrl('adminhtml/sales_order_shipment/new/order_id/' . $order_id) . '">Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/deletelabel/order_id/' . $order_id . '/direction/' . $direction) . '">Delete Label</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/print/imname/' . $upsl['img_name']) . '" target="_blank">Print Label</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $upsl['trackingnumber'] . '.html" target="_blank">Print Html image</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getUrl('upslabelinv/adminhtml_pdflabels/onepdf/order_id/' . $order_id . '/type/' . $direction) . '.html" target="_blank">Print PDF</a>';
                        if (file_exists(Mage::getBaseDir('media') . '/upslabel/label/' . "HVR" . $upsl['trackingnumber'] . ".html")) {
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/HVR' . $upsl['trackingnumber'] . '.html" target="_blank">Print High Value report</a>';
                        }
                        echo '
    <br /><br />
    Tracking Number ' . $upsl['trackingnumber'] . '
    <br /><br /><a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $upsl['img_name'] . '" target="_blank"><img src="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $upsl['img_name'] . '" /></a>';
                    } else {
                        //return 333;
                    }
                } else {
                    if ($this->_without === false) {
                        echo $upsl['error'];
                    } else {
                        $mail = new Zend_Mail();
                        $mail->setBodyText($upsl['error']);
                        $mail->setFrom(Mage::getStoreConfig('upslabelinv/way/emailerror'), Mage::app()->getStore()->getName());
                        $mail->addTo(Mage::getStoreConfig('upslabelinv/way/emailerror'), 'Error');
                        $mail->setSubject('Ups label error for order number ' . $order->getIncrementId() . ' and type ' . $direction);
                        $mail->send();
                    }

                    if ($this->_throwExceptions === true) {
                        throw new Exception($upsl['error']);
                    }
                }
            } else {
                if ($this->_without === false) {
                    echo "This is not sent by UPS";
                } else {
                    //return 333;
                }
            }
        } else {
            if ($this->_without === false) {
                $new = 'view';
                $sp = $ship_id;
                $ships = 'shipment_id';
                if (!$newsss || $newsss != 'no') {
                    $new = 'new';
                    $ships = 'order_id';
                    $sp = $order_id;
                }
                echo '<h1>' . $collection['title'] . '</h1>
<br />
<a href="' . $this->getUrl('adminhtml/sales_order_shipment/' . $new . '/' . $ships . '/' . $sp) . '">Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/deletelabel/order_id/' . $order_id . '/direction/' . $direction) . '">Delete Label</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/print/imname/' . $collection['labelname']) . '" target="_blank">Print Label</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $collection['trackingnumber'] . '.html" target="_blank">Print Html image</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getUrl('upslabelinv/adminhtml_pdflabels/onepdf/order_id/' . $order_id . '/type/' . $direction) . '.html" target="_blank">Print PDF</a>';
                if (file_exists(Mage::getBaseDir('media') . '/upslabel/label/' . "HVR" . $collection['trackingnumber'] . ".html")) {
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/HVR' . $collection['trackingnumber'] . '.html" target="_blank">Print High Value report</a>';
                }
                echo '
<br /><br />
Tracking Number ' . $collection['trackingnumber'] . '
<br /><br /><a href="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $collection['labelname'] . '" target="_blank"><img src="' . Mage::getBaseUrl('media') . 'upslabelinv/label/' . $collection['labelname'] . '" /></a>';
            }
        }
        //$collection->setOrderId($order_id);
        //$collection->setTitle('Testkklsdfjgdfkljgldfk');
        //$collection->save();
        //echo $collection->getTitle();

        if ($this->_without === false) {
            return parent::_beforeToHtml();
        }
    }

    public function createLabel($order, $direction, $throwExceptions = false)
    {

        $this->_direction = $direction;
        $this->_order_id = $order;
        $this->_without = true;
        $this->_throwExceptions = $throwExceptions;
        $this->_beforeToHtml();
    }

}