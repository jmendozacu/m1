<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ajaxaddress
*/
class Amasty_Ajaxaddress_Block_Customer_Address_Renderer_Default extends Mage_Customer_Block_Address_Renderer_Default
{
    public function getFormat(Mage_Customer_Model_Address_Abstract $address=null)
    {
        $format = parent::getFormat($address);
        
        if ( ('sales_order' == Mage::app()->getRequest()->getControllerName() && 'view' == Mage::app()->getRequest()->getActionName()) || 'amajaxaddress' == Mage::app()->getRequest()->getModuleName())
        {
            // replacing text records with input fields
            if (   ('amajaxaddress' == Mage::app()->getRequest()->getModuleName() && Mage::app()->getRequest()->getParam('isAjax'))
                        && (     ('billing'  == $address->getAddressType() && 'billing'  == Mage::app()->getRequest()->getParam('type'))
                                    || ('shipping' == $address->getAddressType() && 'shipping' == Mage::app()->getRequest()->getParam('type'))     )
                )
            {
                if ('edit' == Mage::app()->getRequest()->getActionName())
                {
                    $format = Mage::helper('amajaxaddress')->replaceFormatWithInputs($address, $format, Mage::app()->getRequest()->getParam('type'));
                }
            }
        }
        return $format;
    }
    
    public function render(Mage_Customer_Model_Address_Abstract $address, $format=null)
    {
        $rendered = parent::render($address, $format);
        $buttons  = '';
        
        if ( (('sales_order' == Mage::app()->getRequest()->getControllerName() || 'adminhtml_sales_order' == Mage::app()->getRequest()->getControllerName()) && 'view' == Mage::app()->getRequest()->getActionName()) || 'amajaxaddress' == Mage::app()->getRequest()->getModuleName())
        {
            if (  ('view' == Mage::app()->getRequest()->getActionName() && 'sales_order' == Mage::app()->getRequest()->getControllerName())
                  || ('billing'  == $address->getAddressType() && 'billing'  != Mage::app()->getRequest()->getParam('type'))
                  || ('shipping' == $address->getAddressType() && 'shipping' != Mage::app()->getRequest()->getParam('type'))
                )
            {
                $buttons = Mage::app()->getLayout()->createBlock('amajaxaddress/adminhtml_sales_order_view_info_button')->setMode('edit')->setAddress($address)->toHtml();
            } elseif (   ('amajaxaddress' == Mage::app()->getRequest()->getModuleName())
                        && (     ('billing'  == $address->getAddressType() && 'billing'  == Mage::app()->getRequest()->getParam('type'))
                                    || ('shipping' == $address->getAddressType() && 'shipping' == Mage::app()->getRequest()->getParam('type'))     )
                        )
            {
                if ('edit' == Mage::app()->getRequest()->getActionName())
                {
                    $buttons = Mage::app()->getLayout()->createBlock('amajaxaddress/adminhtml_sales_order_view_info_button')->setMode('save')->setAddress($address)->toHtml();
                } else 
                {
                    $buttons = Mage::app()->getLayout()->createBlock('amajaxaddress/adminhtml_sales_order_view_info_button')->setMode('edit')->setAddress($address)->toHtml();
                }
            }
        }
        return $rendered . $buttons;
    }
}