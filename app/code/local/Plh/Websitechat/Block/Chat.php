<?php
/*
Version: 0.1.4 http://providelivehelp.com
Date: 11-JUN-2010
Copyright: (c) 2010 Provide Live Help
Created By: Sudhansu Ranjan Mangaraj (modulesoft.com)
*/
class Plh_Websitechat_Block_Chat extends Mage_Core_Block_Template
{

	public function _toHtml(){
		
		$position = Mage::getStoreConfig('websitechat/websitechat/position');
		$avilPage = Mage::getStoreConfig('websitechat/websitechat/pages');
		$curPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName();
		//echo $avilPage . $curPage;
		$isLeft = ($this->getParentBlock() === $this->getLayout()->getBlock('left'));
		$isRight = ($this->getParentBlock() === $this->getLayout()->getBlock('right'));
		
		if($avilPage != '')
		{
			if($avilPage == $curPage)
			{
				if($isLeft && $position == 2){
					return '';
				}
				if($isRight && $position == 1){
					return '';
				}
			}elseif($avilPage == 'default'){
				if($isLeft && $position == 2){
					return '';
				}
				if($isRight && $position == 1){
					return '';
				}
			}
			else{
				return '';
			}
		}
		return parent::_toHtml();
	}

}
