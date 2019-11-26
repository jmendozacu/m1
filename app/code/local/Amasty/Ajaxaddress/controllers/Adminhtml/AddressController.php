<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ajaxaddress
*/
class Amasty_Ajaxaddress_Adminhtml_AddressController extends Mage_Adminhtml_Controller_Action
{
    protected function _initOrder()
    {
        $orderId = $this->getRequest()->getPost('order_id');
        $order   = Mage::getModel('sales/order')->load($orderId);
        Mage::register('current_order', $order);
    }
    
    protected function _sendResponse()
    {
        $parentBlock = $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_info', 'order_tab_info');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_info', 'order_info', array('template' => 'sales/order/view/info.phtml'))->setParentBlock($parentBlock)->toHtml()
        );
    }
    
    protected function _saveOrder()
    {
        $order   = Mage::registry('current_order');
        $data    = $this->getRequest()->getPost('amajaxaddress');
        $address = null;
        switch ($this->getRequest()->getPost('type'))
        {
            case 'shipping':
                $address = $order->getShippingAddress();
            break;
            case 'billing':
                $address = $order->getBillingAddress();
            break;
        }
        if ($address)
        {
            foreach ($data as $field => $value)
            {
                if ('region' == $field) {
                    $address->setData('region_id', '');
                }
                if ('region_id' == $field) {
                    $address->setData('region', '');
                }
                $address->setData($field, trim($value));
            }
            $address->save();
            switch ($this->getRequest()->getPost('type'))
            {
                case 'shipping':
                    $order->setShippingAddress($address);
                break;
                case 'billing':
                    $order->setBillingAddress($address);
                break;
            }
            $order->save();
        }
        
        if ($order && version_compare(Mage::getVersion(), '1.4.1.1', '>='))
        {
            // should update name on the grid as well
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $table      = Mage::getResourceModel('sales/order')->getTable('sales/order_grid');
            $field      = ( ('shipping' == $this->getRequest()->getPost('type')) ? 'shipping_name' : 'billing_name' );
            $sql        = ' UPDATE ' . $table . ' SET `' . $field . '` = "' . $data['firstname'] . ' ' . $data['lastname'] . '" WHERE entity_id = "' . $order->getId() . '"' ;
            $connection->query($sql);
        }
    }
    
    public function editAction()
    {
        $this->_initOrder();
        if (1 == $this->getRequest()->getParam('forcesave')) // this param is set when changing country in the drop-down
        {
            // will save only country, so we can take the list of regions for this country.
            $this->_saveOrder();
        }
        $this->_sendResponse();
    }
    
    public function saveAction()
    {
        $this->_initOrder();
        $this->_saveOrder();
        $this->_sendResponse();
    }
}