<?php
class Godogi_Request_Model_Request extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('godogi_request/request');
    }
}