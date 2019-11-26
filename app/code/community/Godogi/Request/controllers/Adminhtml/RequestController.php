<?php
class Godogi_Request_Adminhtml_RequestController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {  
        $this->loadLayout();
        $this->renderLayout();
    }
}