<?php
class Godogi_Request_Model_Resource_Request_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
        $this->_init('godogi_request/request');
    }
}