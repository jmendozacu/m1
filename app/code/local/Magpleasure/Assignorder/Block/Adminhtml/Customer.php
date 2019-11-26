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

class Magpleasure_Assignorder_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
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

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer';
        $this->_blockGroup = 'assignorder';
        $this->_headerText = $this->_helper()->__('Assign Order (#%s) to Customer', $this->_helper()->_order()->getOrder()->getIncrementId());
        parent::__construct();
        $this->removeButton('add');

        $backUrl = $this->getUrl('adminhtml/sales_order/view', array(
                                                                'order_id' => $this->getRequest()->getParam('order_id'),
                                                            ));

        $resetUrl = $this->getUrl('assignorder/order/customerSelect', array(
                                                                'order_id' => $this->getRequest()->getParam('order_id'),
                                                            ));
        $this->addButton('back', array(
            'label'     => $this->_helper()->__("Back"),
            'onclick'   => "window.location = '{$backUrl}';",
            'class'     => 'back',
        ));

        $this->addButton('reset', array(
            'label'     => $this->_helper()->__("Reset"),
            'onclick'   => "window.location = '{$resetUrl}';",
            'class'     => '',
        ));

    }
}