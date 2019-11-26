<?php
/*
Version: 0.1.4 http://providelivehelp.com
Date: 11-JUN-2010
Copyright: (c) 2010 Provide Live Help
Created By: Sudhansu Ranjan Mangaraj (modulesoft.com)
*/

class Plh_Websitechat_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout();
    }
}
