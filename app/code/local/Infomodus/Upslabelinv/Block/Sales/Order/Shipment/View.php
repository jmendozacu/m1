<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    public function __construct()
    {
        parent::__construct();
        $ship_id = $this->getShipment()->getId();
        if ($ship_id) {
            $order = Mage::getModel('sales/order')->load($this->getShipment()->getOrderId());
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            $onlyups = Mage::getStoreConfig('upslabelinv/profile/onlyups');

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
                if ($shipByUps == 'ups' || $onlyups == 0 || $shipMethodArray[0] == 'caship' || $shipMethodArray[0] == 'upstablerates') {
                    if ((int)$shipWay == 3) {
                        $this->_addButton('order_label_to', array(
                            'label' => Mage::helper('sales')->__('UPS Label to'),
                            'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/new/no/ship_id/' . $ship_id . '/direction/to') . '\')',
                            'class' => 'go'
                        ));
                    }
                    $this->_addButton('order_label_from', array(
                        'label' => Mage::helper('sales')->__('UPS Label from'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/new/no/ship_id/' . $ship_id . '/direction/from') . '\')',
                        'class' => 'go'
                    ));
                    $this->_addButton('order_label_to2', array(
                        'label' => Mage::helper('sales')->__('UPS Label to 2'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/new/no/ship_id/' . $ship_id . '/direction/to2') . '\')',
                        'class' => 'go'
                    ));
                }
            } else {
                $this->_addButton('order_label_from', array(
                    'label' => Mage::helper('sales')->__('UPS Label from'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/new/no/ship_id/' . $ship_id . '/direction/from') . '\')',
                    'class' => 'go'
                ));
                $this->_addButton('order_label_to', array(
                    'label' => Mage::helper('sales')->__('UPS Label to'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabelinv/adminhtml_upslabelinv/showlabel/order_id/' . $this->getShipment()->getOrderId() . '/new/no/ship_id/' . $ship_id . '/direction/to') . '\')',
                    'class' => 'go'
                ));
            }
        }
    }
}
