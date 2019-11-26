<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ajaxaddress
*/
class Amasty_Ajaxaddress_Block_Adminhtml_Sales_Order_View_Info_Button extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('amajaxaddress/button.phtml');
        return $this;
    }
    
    public function getEditUrl()
    {
        $url = $this->getUrl('amajaxaddress/adminhtml_address/edit');
        if (Mage::getStoreConfig('web/secure/use_in_adminhtml'))
        {
            $url = str_replace(Mage::getStoreConfig('web/unsecure/base_url'), Mage::getStoreConfig('web/secure/base_url'), $url);
        }
        return $url;
    }
    
    public function getSaveUrl()
    {
        $url = $this->getUrl('amajaxaddress/adminhtml_address/save');
        if (Mage::getStoreConfig('web/secure/use_in_adminhtml'))
        {
            $url = str_replace(Mage::getStoreConfig('web/unsecure/base_url'), Mage::getStoreConfig('web/secure/base_url'), $url);
        }
        return $url;
    }
}