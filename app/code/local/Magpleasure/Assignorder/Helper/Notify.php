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

class Magpleasure_Assignorder_Helper_Notify extends Mage_Core_Helper_Abstract
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
     * Notify customer
     *
     * @param integer $orderId
     * @param integer $customerId
     * @return Magpleasure_Assignorder_Helper_Notify
     */
    public function notifyCustomer($orderId, $customerId)
    {
        if (!$this->_helper()->configNotificationEnabled()){
            return $this;
        }

        $order = $this->_helper()->_order()->getOrder($orderId);
        $store = $order->getStore();
        $storeId = $order->getStoreId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $vars = array(
            'order' => $order,
            'customer' => $customer,
            'store' => $store,
        );

        $template = Mage::getStoreConfig('assignorder/notification/template', $storeId);
        $sender = Mage::getStoreConfig('assignorder/notification/identity', $storeId);
        $copyTo = Mage::getStoreConfig('assignorder/notification/copy_to', $storeId);
        $receivers = array($customer->getEmail());

        if ($copyTo){
            $copyReceivers = explode(",", $copyTo);
            $receivers = array_merge($receivers, $copyReceivers);
        }

        foreach ($receivers as $receiver){
            /** @var Mage_Core_Model_Email_Template $mailTemplate  */
            $mailTemplate = Mage::getModel('core/email_template');
            try {
                $mailTemplate
                    ->setDesignConfig(array('area' => 'frontend', 'store'=>$storeId))
                    ->sendTransactional(
                        $template,
                        $sender,
                        trim($receiver),
                        $customer->getName(),
                        $vars,
                        $storeId
                    )
                ;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

}