<?php
class Scommerce_UpdateEmail_OrderController extends Mage_Adminhtml_Controller_Action {
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('sales/order');
	}

    public function saveAction()
    {
        $result = array();
        $result['success'] = 0;

        $email = $this->getRequest()->getParam('email');
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        $validator = new Zend_Validate_EmailAddress();

        if ($order->getId() && $validator->isValid($email)) {
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $table = $resource->getTableName('sales/order');

            $query = "UPDATE {$table} SET customer_email = '" . $email. "' WHERE entity_id = " . $order->getId();
            $writeConnection->query($query);

            $result['success'] = 1;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}