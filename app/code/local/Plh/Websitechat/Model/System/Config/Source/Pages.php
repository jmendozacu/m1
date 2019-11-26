<?php
/*
Version: 0.1.4 http://providelivehelp.com
Date: 11-JUN-2010
Copyright: (c) 2010 Provide Live Help
Created By: Sudhansu Ranjan Mangaraj (modulesoft.com)
*/

class Plh_Websitechat_Model_System_Config_Source_Pages
{

    public function toOptionArray()
	{
	    return array(
            array('value'=>'0', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')),
			array('value'=>'default', 'label'=>Mage::helper('adminhtml')->__('All Pages')),
			array('value'=>'catalog', 'label'=>Mage::helper('adminhtml')->__('Catalog')),
			array('value'=>'catalogsearch', 'label'=>Mage::helper('adminhtml')->__('Search')),
			array('value'=>'customer', 'label'=>Mage::helper('adminhtml')->__('Customer Account')),
			array('value'=>'checkout', 'label'=>Mage::helper('adminhtml')->__('Cart')),
			array('value'=>'cms', 'label'=>Mage::helper('adminhtml')->__('CMS Page'))
			
        );
    }

}
