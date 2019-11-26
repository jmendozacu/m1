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
 * Customercredit Observer Model
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Observer
{

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Customercredit_Model_Observer
     */
    const XML_PATH_DISABLE_GUEST_CHECKOUT   = 'catalog/downloadable/disable_guest_checkout';
    public function controllerActionPredispatch($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function customercreditPaymentMethod($observer)
    {
        $block = $observer['block'];
        if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods) {
            $requestPath = $block->getRequest()->getRequestedRouteName()
                . '_' . $block->getRequest()->getRequestedControllerName()
                . '_' . $block->getRequest()->getRequestedActionName();
            $transport = $observer['transport'];
            $html_addcredit = $block->getLayout()->createBlock('customercredit/payment_form')->renderView();
            $html = $transport->getHtml();
            $html .= '<script type="text/javascript">checkOutLoadCustomerCredit(' . Mage::helper('core')->jsonEncode(array('html' => $html_addcredit)) . ');enableCheckbox();</script>';
            $transport->setHtml($html);
        }
    }

    public function customerSaveAfter($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer->getId())
            return $this;
        $credit_value = Mage::app()->getRequest()->getPost('credit_value');
        $description = Mage::app()->getRequest()->getPost('description');
        $group = Mage::app()->getRequest()->getPost('account');
        $customer_group = $group['group_id'];
        $sign = substr($credit_value, 0, 1);
        if (!$credit_value)
            return $this;
        $credithistory = Mage::getModel('customercredit/transaction')->setCustomerId($customer->getId());
        $customers = Mage::getModel('customer/customer')->load($customer->getId());
        if ($sign == "-") {
            
            $end_credit = $customers->getCreditValue() - substr($credit_value, 1, strlen($credit_value));
            if($end_credit < 0){
                $end_credit = 0;
                $credit_value = -$customers->getCreditValue();
            }
        } 
        else{
            $credithistory->setData('received_credit',$credit_value);
            $end_credit = $customers->getCreditValue() + $credit_value;
        }
        $customers->setCreditValue($end_credit);
         
        $credithistory->setData('type_transaction_id', 1)
            ->setData('detail_transaction', $description)
            ->setData('amount_credit', $credit_value)
            ->setData('end_balance', $customers->getCreditValue())
            ->setData('transaction_time', now())
            ->setData('customer_group_ids', $customer_group);
        try {
            $customers->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercredit')->__($e->getMessage()));
        }
        try {
            $credithistory->save();
        } catch (Mage_Core_Exception $e) {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercredit')->__($e->getMessage()));
        }
        return $this;
    }


    public function orderPlaceAfter($observer)
    {
        $order = $observer['order'];
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
		if (Mage::app()->getStore()->isAdmin()) {
			$customer_id	=	$order->getCustomerId();
		}
        $session = Mage::getSingleton('checkout/session');
        $amount = $session->getBaseCustomerCreditAmount();
        if ($amount && !$session->getHasCustomerCreditItem()) {
            Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id,
                Magestore_Customercredit_Model_TransactionType::TYPE_CHECK_OUT_BY_CREDIT,
                Mage::helper('customercredit')->__('check out by credit for order #').$order->getIncrementId(), $order->getId(), -$amount);
            if($customer_id!=''){
				Mage::getModel('customercredit/customercredit')->changeCustomerCredit(-$amount,$customer_id);
			}
        }
        if($session->getUseCustomerCredit()){
			$session->setBaseCustomerCreditAmount(null)
					->setUseCustomerCredit(false)
					->setHasCustomerCreditItem(false);
			
		}else{
			$session->setBaseCustomerCreditAmount(null)
					->setHasCustomerCreditItem(false);
		}
       
    }

    public function orderSaveAfter($observer)
    {
        $order = $observer->getOrder();
		$customer_id=$order->getCustomerId();
        /*Order Cancel*/
        if (strtoupper($order->getStatus())==strtoupper('canceled')&&(float)(string)$order->getBaseCustomercreditDiscount()>0) {
             $amount_credit=(float)(string)$order->getBaseCustomercreditDiscount();
             $type_id=Magestore_Customercredit_Model_TransactionType::TYPE_CANCEL_ORDER;
             $order_id=$order->getEntityId();
             //$numberorder=100000000+(int)(string)$order_id;
             $transaction_detail="Cancel order #".$order->getIncrementId();         
             Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id,$type_id,$transaction_detail,$order_id,$amount_credit);
             $customer=Mage::getModel('customer/customer')->load($customer_id);
             $creditbefore=$customer->getCreditValue()+$amount_credit;
             $customer->setCreditValue($creditbefore);
             $customer->save();
            return true;
        }

        /*
         * Submit invoice
         */
        if(strtoupper($order->getStatus())==strtoupper('complete'))
        {
            /* if is user customer credit discount */
//            if($order->getBaseCustomercreditDiscount()>0){
//                $base_credit_discount=$order->getBaseCustomercreditDiscount();
//                $credit_discount=$order->getCustomercreditDiscount();
//                $order->setBaseTotalPaid($order->getBaseTotalPaid()-$base_credit_discount);
//                $order->setTotalPaid($order->getTotalPaid()-$credit_discount);
//                $order->save();
//            }
//            else{
                $product_credit_value = 0;
                foreach($order->getAllItems() as $item){
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    else if ($item->getHasChildren())
                    {
                        continue;
                    }
                    else if($item->getProductType()=='customercredit')
                    {
                        $product_credit_value += ((float)$item->getPrice())*((float)$item->getQtyOrdered());
                    }
                    /*if in order have credit product value*/
                    if($product_credit_value>0){
                    Mage::getModel('customercredit/transaction')->addTransactionHistory($order->getCustomerId(),
                        Magestore_Customercredit_Model_TransactionType::TYPE_BUY_CREDIT,
                        "buy credit ".$product_credit_value." from store ", $order->getId(), $product_credit_value);

                    Mage::getModel('customercredit/customercredit')
                       ->addCreditToFriend($product_credit_value,$customer_id);
                    }
                }
//            }
            

        }
    }
    public function creditmemoSaveAfter(Varien_Event_Observer $observer) {
        //refund order into credit
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $data = Mage::app()->getRequest()->getPost('creditmemo');
		// 2019-01-05 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Fix the "Cannot save the credit memo" issue in Magento 1»:
		// https://www.upwork.com/ab/f/contracts/21337759
        if (@$data['refund_creditbalance_return_enable']) {
			$orderid =$creditmemo->getOrderId();
			$order = Mage::getSingleton('sales/order');$order->load($orderid);
			$grand_total=$order->getGrandTotal();
			$amount_credit = $order->getCustomercreditDiscount();
			$customer_id=$creditmemo->getCustomerId();
			if($customer_id){
				// 2019-01-05 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// «Fix the "Cannot save the credit memo" issue in Magento 1»:
				// https://www.upwork.com/ab/f/contracts/21337759
				$refund_creditbalance_return = (float)(string)@$data['refund_creditbalance_return'];
				$maxcredit=$grand_total;
					if($refund_creditbalance_return > $maxcredit){
						Mage::throwException(Mage::helper('customercredit')->__('Credit amount cannot exceed order amount.'));
					}
					else{
						$amount_credit += $refund_creditbalance_return;
						$type_id=Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT;
						$order_id=$creditmemo->getOrderId();
						$transaction_detail="Refund order #".$order->getIncrementId()." into customer credit";
					}
					$amount_credit1=0;
					foreach($order->getAllItems() as $item){
							if ($item->getParentItemId()) {
								continue;
							}
							else if ($item->getHasChildren())
							{
								continue;
							}
							else if($item->getProductType()=='customercredit')
							{
								$amount_credit1 += ((float)$item->getPrice())*((float)$item->getQtyOrdered());
							}
				   }
							/*if in order have credit product value*/
							if($amount_credit1>0){
								$amount_credit -=$amount_credit1;
								$type_id=Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_CREDIT_PRODUCT;
								$order_id=$creditmemo->getOrderId();
							   // $numberorder=100000000+(int)(string)$order_id;
								$transaction_detail="Refund order #".$order->getIncrementId();
							}


				  Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id,$type_id,$transaction_detail,$order_id,$amount_credit);
				  $customer=Mage::getModel('customer/customer')->load($customer_id);
				  $credit_value = $customer->getCreditValue()+$amount_credit;
						  if($credit_value < 0){
										$credit_value = 0;
								}
				  $customer->setCreditValue($credit_value);
				  $customer->save();
			}
		}
    }
    public function adminhtmlCatalogProductSaveAfter($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $back = $action->getRequest()->getParam('back');
        $session = Mage::getSingleton('customercredit/session');
        $creditproductsession = $session->getCreditProductCreate();

        if ($back || !$creditproductsession)
            return $this;
        $type = $action->getRequest()->getParam('type');
        if (!$type) {
            $id = $action->getRequest()->getParam('id');
            $type = Mage::getModel('catalog/product')->load($id)->getTypeId();
        }
        if (!$type)
            return $this;

        $reponse = Mage::app()->getResponse();
        $url = Mage::getModel('adminhtml/url')->getUrl("customercreditadmin/adminhtml_creditproduct/index");
        $reponse->setRedirect($url);
        $reponse->sendResponse();
        $session->unsetData('credit_product_create');
        return $this;
    }

    //event checkout_allow guest
    public function isAllowedGuestCheckout(Varien_Event_Observer $observer)
    {
        $quote  = $observer->getEvent()->getQuote();
        /* @var $quote Mage_Sales_Model_Quote */
        $store  = $observer->getEvent()->getStore();
        $result = $observer->getEvent()->getResult();
        $session = Mage::getSingleton('checkout/session');
        $isContain = false;

        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) &&
            $product->getTypeId() == 'customercredit') {
                $isContain = true;
            }
        }
        $session->setHasCustomerCreditItem(true);
        
        if ($isContain && Mage::getStoreConfigFlag(self::XML_PATH_DISABLE_GUEST_CHECKOUT, $store)) {
            $result->setIsAllowed(false);
        }

        return $this;
    }
	public function paypal_prepare_line_items($observer) {

		$paypalCart = $observer->getEvent()->getPaypalCart();
		if ($paypalCart){
			$salesEntity = $paypalCart->getSalesEntity();
			
			if($salesEntity->getCustomercreditDiscount() > 0){
				$paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,abs((float)$salesEntity->getCustomercreditDiscount()),Mage::helper('customercredit')->__('Customer Credit'));				
						
			}
		}

	}
}
