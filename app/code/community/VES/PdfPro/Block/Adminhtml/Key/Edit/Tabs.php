<?php

class VES_PdfPro_Block_Adminhtml_Key_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('apikey_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('pdfpro')->__('API Key Information'));
  }

  protected function _beforeToHtml()
  {
  		Mage::dispatchEvent('ves_pdfpro_apikey_tabs_before',array('block'=>$this));
	  	$this->addTab('form', array(
	  			'label'     => Mage::helper('pdfpro')->__('API Key Information'),
	  			'title'     => Mage::helper('pdfpro')->__('API Key Information'),
	  			'content'   => $this->getLayout()->createBlock('pdfpro/adminhtml_key_edit_tab_form')->toHtml(),
	  	));
	  	Mage::dispatchEvent('ves_pdfpro_apikey_tabs_after',array('block'=>$this));
      	return parent::_beforeToHtml();
  }
}