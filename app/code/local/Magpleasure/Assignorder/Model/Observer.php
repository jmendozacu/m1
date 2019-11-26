<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Assignorder
 * @version    1.0.1
 * @copyright  Copyright (c) 2012 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Assignorder_Model_Observer
{
    /**
     * Helper
     *
     * @return Magpleasure_Assignorder_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('assignorder');
    }

    /**
     * Backend URL Model
     *
     * @return Mage_Adminhtml_Model_Url
     */
    protected function _getBackendUrlModel()
    {
        return Mage::getSingleton("adminhtml/url");
    }

    public function generateBlockAfter($event)
    {
        $block = $event->getBlock();

        # Order View
        if ($block && ($block->getNameInLayout() == 'sales_order_edit')){
            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View){

                if ($this->_helper()->_order()->isGuestOrder() && $this->_helper()->isAllowed()){
                    $url = $this->_getBackendUrlModel()->getUrl('assignorder/order/customerSelect', array(
                        'order_id' => Mage::app()->getRequest()->getParam('order_id'),
                    ));
                    $block->addButton('assignOrder', array(
                        'label'     => $this->_helper()->__("Assign to Customer"),
                        'onclick'   => "window.location = '{$url}';",
                        'class'     => 'go',
                    ));
                }

            }
        }



    }
}