<?php
class Infomodus_Adminhtml_Model_System_Config_Source_Shipping_Upstablerates
{
    public function toOptionArray($type=null)
    {
        $tableRate = Mage::getSingleton('upstablerates_shipping/carrier_upstablerates');
        $arr = array();
        
        foreach ($tableRate->getCode('condition_name') as $k=>$v) 
        {
        	$arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}