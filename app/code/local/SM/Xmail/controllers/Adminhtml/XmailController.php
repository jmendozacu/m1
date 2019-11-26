<?php

class SM_Xmail_Adminhtml_XmailController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            if (empty($data)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            $order = Mage::getModel('sales/order')->loadByIncrementId($data['sm']['orderid']);
            $order->setCustomer_email($data['sm']['email']);
            $order->save();
            if($data['sm']['confirm'])
                $order->sendNewOrderEmail();
            $message = $this->__('Order #'.$data['sm']['orderid'].' has been edited successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }

    public function searchAction()
    {
        $orderid = $this->getRequest()->getPost('order_id');
        $_order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
        $email = $_order->getCustomerEmail();
        if ($email)
            echo $email;
        else
            echo 0;
    }
}