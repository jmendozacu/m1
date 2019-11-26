<?php
/*
Version: 0.1.4 http://providelivehelp.com
Date: 11-JUN-2010
Copyright: (c) 2010 Provide Live Help
Created By: Sudhansu Ranjan Mangaraj (modulesoft.com)
*/
class Plh_Websitechat_Model_Config extends Varien_Object
{
	/**
     * Get the website chat configuration data
     */
	public function getWebsiteChat($opt=''){
		$content = Mage::getStoreConfig('websitechat/websitechat/'.$opt);
		return (($content!='')? $content : '' );
	}

}
