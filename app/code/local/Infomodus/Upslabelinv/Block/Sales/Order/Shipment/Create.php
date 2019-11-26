<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Sales_Order_Shipment_Create extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create
{
    public function __construct()
    {
        /*Mage::log(__METHOD__);*/
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::__construct();

        //$this->_updateButton('save', 'label', Mage::helper('sales')->__('Submit Shipment'));
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $order_idd = $this->getRequest()->getParam('order_id');
        if ($this->getRequest()->getParam('order_id')) {
            $order = Mage::getModel('sales/order')->load($order_idd);
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            $onlyups = Mage::getStoreConfig('upslabelinv/profile/onlyups');
            $collection = Mage::getModel('upslabelinv/upslabelinv')->load($order_idd, 'order_id');
            $shipfromCountryCode = Mage::getStoreConfig('upslabelinv/shipfrom/countrycode');
            $shiping_adress = $order->getShippingAddress();
            $shiptoCountryCode = $shiping_adress['country_id'];


            $shipMethodArray = explode('_', $order->getShippingMethod());
            $shipWay = 0;
            if ($shipMethodArray[0] == 'caship') {
                $caship = Mage::getModel('caship/method')->load($shipMethodArray[1]);
                $shipWay = $caship->getDirectionType();
            } else if ($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) {
                $upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->loadPk($shipMethodArray[2]);
                $shipWay = $upstablerates['way'];
            }
            if ($shipWay > 1) {
                if ($collection->getOrderId() == $order_idd || (($shipByUps == 'ups' && $shipfromCountryCode == $shiptoCountryCode) || $shipMethodArray[0] == 'caship' || $shipMethodArray[0] == 'upstablerates')) {
                    if ((int)$shipWay == 3) {
                        $this->_addButton('order_label_to', array(
                            'label' => Mage::helper('sales')->__('UPS Label to'),
                            'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/direction/to') . '\')',
                            'class' => 'go'
                        ));
                    }
                    $this->_addButton('order_label_from', array(
                        'label' => Mage::helper('sales')->__('UPS Label from'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/direction/from') . '\')',
                        'class' => 'go'
                    ));
                    $this->_addButton('order_label_to2', array(
                        'label' => Mage::helper('sales')->__('UPS Label to 2'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/direction/to2') . '\')',
                        'class' => 'go'
                    ));
                } else if ($onlyups == 0 || $shipfromCountryCode != $shiptoCountryCode) {
                    if ((int)$shipWay == 3) {
                        $this->_addButton('order_label_to', array(
                            'label' => Mage::helper('sales')->__('UPS Label to'),
                            'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/intermediate/order_id/' . $this->getShipment()->getOrderId() . '/direction/to') . '\')',
                            'class' => 'go'
                        ));
                    }
                    $this->_addButton('order_label_from', array(
                        'label' => Mage::helper('sales')->__('UPS Label from'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/intermediate/order_id/' . $this->getShipment()->getOrderId() . '/direction/from') . '\')',
                        'class' => 'go'
                    ));
                    $this->_addButton('order_label_to2', array(
                        'label' => Mage::helper('sales')->__('UPS Label to 2'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/intermediate/order_id/' . $this->getShipment()->getOrderId() . '/direction/to2') . '\')',
                        'class' => 'go'
                    ));
                }
            } else {
                $this->_addButton('order_label_from', array(
                    'label' => Mage::helper('sales')->__('UPS Label from'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/intermediate/order_id/' . $this->getShipment()->getOrderId() . '/direction/from') . '\')',
                    'class' => 'go'
                ));
                if ($collection->getOrderId() == $order_idd) {
                    $this->_addButton('order_label_to', array(
                        'label' => Mage::helper('sales')->__('UPS Label to'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/direction/to') . '\')',
                        'class' => 'go'
                    ));
                } else {
                    $this->_addButton('order_label_to', array(
                        'label' => Mage::helper('sales')->__('UPS Label to'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/intermediate/order_id/' . $this->getShipment()->getOrderId() . '/direction/to') . '\')',
                        'class' => 'go'
                    ));
                }
            }
        }
    }
}
