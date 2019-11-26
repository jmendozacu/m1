<?php
/**
 * AutomaticInvoice Abstract Order Class
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
abstract class Vianetz_AutomaticInvoice_Model_Workflow_Order_SendEmail_Abstract
{
    /**
     * Return the source type, e.g. invoice or shipment.
     *
     * @return string
     */
    abstract public function getSourceType();

    /**
     * Send invoice/shipment email and notify customer if enabled in configuration.
     *
     * @param Mage_Sales_Model_Abstract $sourceModel invoice/shipment instance
     *
     * @return boolean returns true if sending email was successful, false otherwise
     */
    public function start(Mage_Sales_Model_Abstract $sourceModel)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $sourceModel->getOrder();

        if ($this->isEnabled($order) !== true) {
            $paymentMethod = Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')->getPaymentMethod($order);
            Mage::helper('automaticinvoice')->log('INFO: ' . ucfirst($this->getSourceType()) . ' notification is not active for current order status and payment method combination (' . $order->getStatus() . ', ' . $paymentMethod . '), skipping. Please check the configuration setting under System > Configuration > AutomaticInvoice > ' . ucfirst($this->getSourceType()) . '.', LOG_INFO);
            return false;
        }

        try {
            Mage::getModel('automaticinvoice/action_notify')
                ->setSourceModel($sourceModel)
                ->run();
        } catch (Exception $exception) {
            Mage::helper('automaticinvoice')->log('Error occurred while sending email: ' . $exception->getMessage() . $exception->getTraceAsString());
        }

        return true;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    protected function isEnabled(Mage_Sales_Model_Order $order)
    {
        return Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')
            ->isPaymentMethodAndOrderStatusActionEnabled('is_trigger_email', $order, $this->getSourceType());
    }
}