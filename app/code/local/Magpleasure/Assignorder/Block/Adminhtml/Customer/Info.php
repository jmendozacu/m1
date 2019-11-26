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

class Magpleasure_Assignorder_Block_Adminhtml_Customer_Info extends Mage_Adminhtml_Block_Template
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
        parent::__construct();
        $this->setTemplate('assignorder/order/info.phtml');
    }

    public function getOrderId()
    {
        return $this->_helper()->_order()->getOrder()->getId();
    }

    public function getIncrementId()
    {
        return $this->_helper()->_order()->getOrder()->getIncrementId();
    }

    public function getAssignUrl()
    {
        return $this->getUrl('assignorder/order/assignToCustomer', array(
            'order_id' => "{{order_id}}",
        ));
    }

    public function getConfSendEmail()
    {
        return $this->_helper()->configNotificationEnabled();
    }

    public function getConfOverwriteName()
    {
        return false;
    }

}