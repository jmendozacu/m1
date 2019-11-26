<?php
/*
Version: 0.1.4 http://providelivehelp.com
Date: 11-JUN-2010
Copyright: (c) 2010 Provide Live Help
Created By: Sudhansu Ranjan Mangaraj (modulesoft.com)
*/

class Plh_Websitechat_Model_System_Config_Source_Postions
{

    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('adminhtml')->__('--Please Select--')),
			array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('Left')),
			array('value'=>2, 'label'=>Mage::helper('adminhtml')->__('Right'))
        );
    }

}
