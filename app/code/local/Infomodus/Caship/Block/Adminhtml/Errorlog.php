<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Caship_Block_Adminhtml_Errorlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_errorlog';
        $this->_blockGroup = 'caship';
        $this->_headerText = Mage::helper('caship')->__('Infomodus Shipping Errors log');
        parent::__construct();
        $this->_removeButton('add');
    }
}