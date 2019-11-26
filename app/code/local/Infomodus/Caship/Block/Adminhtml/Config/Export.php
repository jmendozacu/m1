<?php

/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 19.06.15
 * Time: 0:21
 */
class Infomodus_Caship_Block_Adminhtml_Config_Export extends Mage_Adminhtml_Block_System_Config_Form_Field  implements Varien_Data_Form_Element_Renderer_Interface
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonBlock = Mage::app()->getLayout()->createBlock('adminhtml/widget_button');

        $data = array(
            'label'     => Mage::helper('adminhtml')->__('Csv export'),
            'onclick'   => 'setLocation(\''.Mage::helper('adminhtml')->getUrl("/caship_method/export") . '\' )',
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}