<?php
class Infomodus_Caship_Model_Config_ShippingCompany
{
    public function toOptionArray($isCustom = true)
    {
        $arr = array();
        if ($isCustom === true) {
            $arr[] = array('value' => 'custom', 'label' => 'Custom');
        }
        $arr[] = array('value' => 'ups', 'label' => 'UPS');
        /*$arr[] = ['value' => 'dhl', 'label' => 'DHL'];*/
        return $arr;
    }
}