<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 28.12.11
 * Time: 9:38
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabelinv_Model_Observer
{
    public function initUpslabel($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'sales_order') {
            $block->addItem('upslabelinv_pdflabels', array(
                'label' => Mage::helper('sales')->__('Print UPS Shipping Labels'),
                'url' => Mage::app()->getStore()->getUrl('upslabelinv/adminhtml_pdflabels'),
            ));
        }
        return $this;
    }
}
