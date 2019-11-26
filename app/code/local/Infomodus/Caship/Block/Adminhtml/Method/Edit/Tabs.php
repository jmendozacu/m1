<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Block_Adminhtml_Method_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('method_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('caship')->__('Infomodus Shipping Method Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('caship')->__('Information'),
            'title'     => Mage::helper('caship')->__('Information'),
            'content'   => $this->getLayout()->createBlock('caship/adminhtml_method_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}