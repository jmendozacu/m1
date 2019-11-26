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
	class Magestore_Customercredit_Model_Total_Order_Creditmemo_Discount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {
		public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo){
			$order = $creditmemo->getOrder();
			if ($order->getPayment()->getMethod() == 'customercredit')
				return $this;
			
			if ($order->getBaseCustomercreditDiscount() && $order->getCustomercreditDiscount()){
				 $base_customer_credit_discount=$order->getBaseCustomercreditDiscount();
				 $customer_credit_discount = $order->getCustomercreditDiscount();
				 
				if ($creditmemo->getBaseGrandTotal() - $base_customer_credit_discount < 0){
					$creditmemo->setBaseCustomercreditDiscount($creditmemo->getBaseGrandTotal());
					$creditmemo->setCustomercreditDiscount($creditmemo->getGrandTotal());
					$creditmemo->setBaseGrandTotal(0);
					$creditmemo->setGrandTotal(0);
				}else{
					$creditmemo->setBaseCustomercreditDiscount($base_customer_credit_discount);
					$creditmemo->setCustomercreditDiscount($customer_credit_discount);
					$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$base_customer_credit_discount);
					$creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$customer_credit_discount);
				}	
			}
		}
			
	}
?>