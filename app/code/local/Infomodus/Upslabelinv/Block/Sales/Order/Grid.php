<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabelinv_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('upslabelinv_pdflabels', array(
             'label'=> Mage::helper('sales')->__('Print UPS Shipping Labels'),
             'url'  => $this->getUrl('upslabelinv/adminhtml_pdflabels'),
        ));

        return $this;
    }
}
