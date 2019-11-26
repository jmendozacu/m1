<?php
class Infomodus_Adminhtml_Model_System_Config_Backend_Shipping_Upstablerates extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
		Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->uploadAndImport($this);
    }
}
