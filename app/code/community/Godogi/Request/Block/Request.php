<?php
class Godogi_Request_Block_Request extends Mage_Core_Block_Template
{
	public function getFormAction()
    {
        return $this->getUrl('*/*/register');
    }
}