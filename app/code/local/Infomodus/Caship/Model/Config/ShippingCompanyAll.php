<?php
class Infomodus_Caship_Model_Config_ShippingCompanyAll
{
    public function toOptionArray($isCustom = true)
    {
        $arr = array();
        if ($isCustom === true) {
            $arr[] = array('value' => 'custom', 'label' => 'Custom');
        }
        $arr[] = array('value' => 'ups', 'label' => Mage::getStoreConfig('carriers/ups/title'));
        if(Mage::helper('core')->isModuleOutputEnabled("Infomodus_Upslabelinv")) {
            $arr[] = array('value' => 'upsinfomodus', 'label' => 'UPS Infomodus');
        }
        /*$arr[] = ['value' => 'dhl', 'label' => Mage::getStoreConfig('carriers/dhlint/title')];
        if(Mage::helper('core')->isModuleOutputEnabled("Infomodus_Dhllabel")) {
            $arr[] = ['value' => 'dhlinfomodus', 'label' => 'DHL Infomodus'];
        }*/
        return $arr;
    }
}