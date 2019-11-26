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
 * Customercredit Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Total_Order_Invoice_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice){
		$order = $invoice->getOrder();
		if ($order->getPayment()->getMethod() == 'customercredit')
				return $this;
		if ($order->getBaseCustomercreditDiscount() && $order->getCustomercreditDiscount()){
			 $base_customer_credit_discount=$order->getBaseCustomercreditDiscount();
           	 $customer_credit_discount = $order->getCustomercreditDiscount();
			 
			if ($invoice->getBaseGrandTotal() - $base_customer_credit_discount < 0){
				$invoice->setBaseCustomercreditDiscount($invoice->getBaseGrandTotal());
				$invoice->setCustomercreditDiscount($invoice->getGrandTotal());
				$invoice->setBaseGrandTotal(0);
				$invoice->setGrandTotal(0);
			}else{
				$invoice->setBaseCustomercreditDiscount($base_customer_credit_discount);
				$invoice->setCustomercreditDiscount($customer_credit_discount);
				$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$base_customer_credit_discount);
				$invoice->setGrandTotal($invoice->getGrandTotal()-$customer_credit_discount);
			}	
			
		}
	}
}