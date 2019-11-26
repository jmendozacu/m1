<?php
class Godogi_Request_Block_Adminhtml_Request extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'godogi_request';
        $this->_controller = 'adminhtml_request';
        $this->_headerText = $this->__('Request');
        parent::__construct();
        $this->_removeButton('add');
    }
}