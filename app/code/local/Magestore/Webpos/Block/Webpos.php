<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpos Block
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Block_Webpos extends Mage_Checkout_Block_Onepage_Abstract
{
    var $configData = array();
    public function __construct() 
    {
        $this->configData = $this->_getConfigData();
        //set default shipping && payment method 
        $this->_setDefaultShippingMethod();
        $this->_setDefaultPaymentMethod();
    }
    
    protected function _setDefaultShippingMethod() 
    {
        $shipping_address = $this->getOnepage()->getQuote()->getShippingAddress();
        $shipping_method = $shipping_address->getShippingMethod();
        if (!$shipping_method || $shipping_method == '') {
            //set default shipping method
            $default_shipping_method = $this->configData['default_shipping'];
            if ($default_shipping_method != '') {
                //Mage::helper('onestepcheckout')->saveShippingMethod($default_shipping_method);
                $this->getOnePage()->getQuote()->getShippingAddress()->setShippingMethod($default_shipping_method);
            }
            else {            
                // if no default shipping method and only one shipping method is available, set it as default
                if ($method = $this->hasOnlyOneShippingMethod()){
                    //Mage::helper('onestepcheckout')->saveShippingMethod($method);
                    $this->getOnePage()->getQuote()->getShippingAddress()->setShippingMethod($method);
                }
            }
        }
        $this->getOnePage()->getQuote()->collectTotals()->save();
    }

    /*
    * set default payment method
    */
    protected function _setDefaultPaymentMethod() 
    {
        $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        if (!$paymentMethod || $paymentMethod == '') {
            $default_payment_method = $this->configData['default_payment'];
            if ($default_payment_method != '') {
                $payment = array('method' => $default_payment_method);
                try {
                    Mage::helper('onestepcheckout')->savePaymentMethod($payment);
                }
                catch (Exception $e) {
                    // ignore error
                }
            }
            else {
            
            }
        }
    }
    
    public function getOnepage() 
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    protected function _getConfigData() 
    {
        return Mage::helper('webpos')->getConfigData();
    }
    
    public function isCustomerLoggedIn() 
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function isVirtual() 
    {
        return $this->getQuote()->isVirtual();
    }
    
    public function hasOnlyOneShippingMethod() 
    {
        $rates = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRatesCollection();
        $rateCodes = array();
        foreach($rates as $rate)    {
            if(!in_array($rate->getCode(), $rateCodes)) {
                $rateCodes[] = $rate->getCode();
            }
        }
        if(count($rateCodes) == 1)  {
            return $rateCodes[0];
        }        
        return false;
    }
    
    public function isAjaxBillingField($field_name) 
    {
        $fields = explode(',', $this->configData['ajax_fields']);
        if(in_array($field_name, $fields))    {
            return true;
        }        
        return false;
    }
    
    public function isShowShippingAddress() 
    {
        if($this->getOnepage()->getQuote()->isVirtual())    {
            return false;
        }
        if($this->configData['show_shipping_address'])    {
            return true;
        }
        return false;
    }
    
    public function getBillingAddress() 
    {    
        return $this->getQuote()->getBillingAddress();        
    }
    
    public function getCountryHtmlSelect($type)
    {
        if($type == 'billing')    {
            $address = $this->getQuote()->getBillingAddress();    
        }
        else    {
            $address = $this->getQuote()->getShippingAddress();
        }
        
        $countryId = $address->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('webpos/general/country_id',Mage::app()->getStore(true)->getId());
        }
        $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'[country_id]')
                ->setId($type.':country_id')
                ->setTitle(Mage::helper('webpos')->__('Country'))
                ->setClass('validate-select')
                ->setValue($countryId)
                ->setOptions($this->getCountryOptions())
                ->setExtraParams('style="width:135px"');

        return $select->getHtml();
    }
    
    public function getShippingAddress() 
    {
        if (!$this->isCustomerLoggedIn()) {
            return $this->getQuote()->getShippingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }
} 

?>