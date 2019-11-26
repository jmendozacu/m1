<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Caship_Block_Adminhtml_Method extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        
        $this->_controller = 'adminhtml_method';
        $this->_blockGroup = 'caship';
        $this->_headerText = Mage::helper('caship')->__('Infomodus Shipping Methods');
        $this->_addButtonLabel = Mage::helper('caship')->__('Add method');

        $data = array(
            'label' =>  Mage::helper('caship')->__('Add method'),
            'class' => 'scalable add',
            'onclick'   => "setLocation('".$this->getUrl('adminhtml/caship_method/new')."')"
        );
        $this->addButton('method_add', $data, 0, 100,  'header', 'header');
        parent::__construct();
        $this->_removeButton('add');
    }
}