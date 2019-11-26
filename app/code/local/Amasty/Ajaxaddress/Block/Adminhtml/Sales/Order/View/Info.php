<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ajaxaddress
*/
class Amasty_Ajaxaddress_Block_Adminhtml_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if (!Mage::app()->getRequest()->getParam('isAjax'))
        {
            $html = '<div id="amajaxaddress_info_wrapper">' . $html . '</div>';
        }
        return $html;
    }
}