<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Assignorder
 * @version    1.0.1
 * @copyright  Copyright (c) 2012 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Assignorder_OrderController extends Mage_Adminhtml_Controller_action
{
    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_helper()->isAllowed();
    }

    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('assignorder');
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('sales/order')
			->_addBreadcrumb($this->_helper()->__('Assign Order to Customer'), $this->_helper()->__('Assign Order to Customer'));
		
		return $this;
	}   
 
	public function customerSelectAction()
    {
        if ($this->_helper()->_order()->isGuestOrder()){
            $this->_initAction()
                ->renderLayout();
        } else {
            $this->_getSession()->addError("Only Guest Order can be assigned to customer.");
            $this->_redirect('adminhtml/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }

    public function customerGridAction()
    {
        $grid = $this->getLayout()->createBlock('assignorder/adminhtml_customer_grid');
        if ($grid){
            $this->getResponse()->setBody($grid->toHtml());
        }
    }

    public function assignToCustomerAction()
    {
        $customerId = $this->getRequest()->getPost('customer_id');
        $sendEmail = !!$this->getRequest()->getPost('send_email');
        $overwriteName = !!$this->getRequest()->getPost('overwrite_name');

        $orderId = $this->getRequest()->getParam('order_id');
        if ($customerId && $orderId){
            $order = $this->_helper()->_order()->getOrder($orderId);
            if ($order->canAssignToCustomer($customerId)){
                $order->assignToCustomer($customerId, $overwriteName, $sendEmail);
                $this->_getSession()->addSuccess($this->_helper()->__("Order was successfully assigned to customer."));
                $this->_redirect('adminhtml/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));

            } else {
                $this->_getSession()->addError($this->_helper()->__("Can't assign customer to this order."));
                $this->_redirect('adminhtml/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
            }
        } else {
            $this->_getSession()->addError($this->_helper()->__("Some data missed."));
            $this->_redirectReferer();
        }
    }

    public function rollbackAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId){
            $order = $this->_helper()->_order()->getOrder($orderId);
            if (($history = $order->getAssignmentHistory()->getLastItem()) && $history->canRollback()){
                try {
                    $history->rollback();
                    $this->_getSession()->addSuccess($this->_helper()->__("The assignment was successfully rolled back."));
                } catch (Exception $e){
                    $this->_getSession()->addError($this->_helper()->__("Can't rollback this order."));
                }
            } else {
                $this->_getSession()->addError($this->_helper()->__("Can't rollback this order."));
            }
        } else {
            $this->_getSession()->addError($this->_helper()->__("Some data missed."));
        }
        $this->_redirectReferer();
    }

}