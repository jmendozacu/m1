<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
class Infomodus_Caship_Model_Config_ShippingSettingsLink
{
    public function getCommentText()
    {
        return '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/caship_method/index").'" target="_blank">'.Mage::helper('adminhtml')->__("Create and edit Shipping Methods").'</a>';
    }
}