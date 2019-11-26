<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('upslabelinv_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('upslabelinv')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('upslabelinv')->__('Item Information'),
          'title'     => Mage::helper('upslabelinv')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}