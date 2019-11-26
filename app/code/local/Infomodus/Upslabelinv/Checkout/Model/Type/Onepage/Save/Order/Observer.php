<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 22.12.11
 * Time: 11:54
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabelinv_Checkout_Model_Type_Onepage_Save_Order_Observer
{
    public function __construct()
    {
    }

    public function after_save_order($observer)
    {
        $event = $observer->getEvent();

        $upslabel = new Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Label_Tabs();
        $shipMethodArray = explode('_', $event->getOrder()->getShippingMethod());
        $shipWay = 0;
        if ($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) {
            $upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->loadPk($shipMethodArray[2]);
            $shipWay = $upstablerates['way'];
        }
        if ($shipMethodArray[0] == 'caship') {
            $upsmethodId = $shipMethodArray[1];
            $cashipModel = Mage::getModel('caship/method')->load($upsmethodId);
            if ($cashipModel) {
                $shipWay = $cashipModel->getDirectionType();
            }
        }
        if ((int)$shipWay == 3) {
            $upslabel->createLabel($event->getOrder()->getId(), 'to');
        }
        if ((int)$shipWay > 1 && (($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) || $shipMethodArray[0] == 'caship')) {
            $upslabel->createLabel($event->getOrder()->getId(), 'from');
        }
        return $this;
    }
}
