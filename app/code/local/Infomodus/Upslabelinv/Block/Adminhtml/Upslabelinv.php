<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_upslabelinv';
    $this->_blockGroup = 'upslabelinv';
    $this->_headerText = Mage::helper('upslabelinv')->__('Item Manager');
    /*$this->_addButtonLabel = Mage::helper('upslabel')->__('Add Item');*/
    parent::__construct();
  }
}