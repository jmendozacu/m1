<?php
/**
 * Mason Web Development
 *
 * @category    RichardMason
 * @package     RichardMason_Profile
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RichardMason_Profile_PressarticlesController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function readAction()
    {
 		$block=Mage::getBlockSingleton('profile/profile');
    	$profile = $block->getProfile();
		if($profile->getData("picture")){
			echo "&nbsp;";
			echo "<img src=\"". Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . "media/" . $profile->getData("picture"). "\" width='950px' />";
			echo "&nbsp;";
		}

    }
    
}