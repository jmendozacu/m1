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
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Controller
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_CheckoutController extends Mage_Core_Controller_Front_Action
{
    /**
     * change use customer credit to spend
     */
    public function setAmountPostAction()
    {  
        $request = $this->getRequest();

        if ($request->isPost()) {
            $session = Mage::getSingleton('checkout/session');
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer_id = $customerData->getId();
            $subtotal = (float)(string)Mage::getSingleton('checkout/session')->getQuote()->getBaseSubtotal();
            $tax = (float)(string)Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_tax_amount');
            $shipping = (float)(string)Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_shipping_amount');
            $shipping_tax = (float)(string)Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_shipping_tax_amount');
            $maxcredit=Mage::helper('customercredit')->getMaxCreditCanUse($customer_id,$subtotal,$tax,$shipping,$shipping_tax);
            
            if (is_numeric($request->getParam('customer_credit'))&& Mage::helper('customercredit')->getGeneralConfig('enable')) {
                $credit_amount = $request->getParam('customer_credit');
                $base_credit_amount = Mage::getModel('customercredit/customercredit')
                    ->getConvertedToBaseCustomerCredit($credit_amount);
                $base_customer_credit = Mage::getModel('customercredit/customercredit')->getBaseCustomerCredit();
                $base_credit_amount = ($base_credit_amount>$base_customer_credit)?$base_customer_credit:$base_credit_amount;
                if($base_credit_amount>$maxcredit){
                    $session->setBaseCustomerCreditAmount($maxcredit);
                }
                else{
                    $session->setBaseCustomerCreditAmount($base_credit_amount);
                }
                    $session->setUseCustomerCredit(true);
                
                
                $this->_redirect('checkout/cart');
            }
            if(is_numeric($request->getParam('credit_amount'))){
                $amount = $request->getParam('credit_amount');
                $base_amount = Mage::getModel('customercredit/customercredit')
                    ->getConvertedToBaseCustomerCredit($amount);
                $base_customer_credit = Mage::getModel('customercredit/customercredit')->getBaseCustomerCredit();
                $base_credit_amount = ($base_amount>$base_customer_credit)?$base_customer_credit:$base_amount;
                if($base_credit_amount>$maxcredit){
                    $session->setBaseCustomerCreditAmount($maxcredit);
                }
                else{
                    $session->setBaseCustomerCreditAmount($base_credit_amount);
                }
                $session->setUseCustomerCredit(true);
                $result = array();
                $result['success'] = 1;
                $result['amount'] = Mage::getModel('customercredit/customercredit')
                    ->getConvertedFromBaseCustomerCredit($session->getBaseCustomerCreditAmount());
                $result['price0'] = 0;
                if($session->getBaseCustomerCreditAmount()==$maxcredit)
                       $result['price0'] = 1;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }
}
