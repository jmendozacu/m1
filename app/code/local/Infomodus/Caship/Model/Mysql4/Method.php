<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Model_Mysql4_Method extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('caship/method', 'caship_id');
    }
}