<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
class Infomodus_Caship_Model_Config_ErrorLogLink
{
    public function getCommentText()
    {
        return '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/caship_errorlog/index").'" target="_blank">'.Mage::helper('caship')->__("Errors log").'</a>';
    }
}