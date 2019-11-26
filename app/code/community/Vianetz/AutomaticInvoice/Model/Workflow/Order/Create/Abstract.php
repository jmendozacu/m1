<?php
/**
 * AutomaticInvoice Abstract Create Order Workflow Class
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
abstract class Vianetz_AutomaticInvoice_Model_Workflow_Order_Create_Abstract
{
    /**
     * Return the source type, e.g. invoice or shipment.
     *
     * @return string
     */
    abstract public function getSourceType();

    /**
     * This method checks if generation is allowed and then triggers the specific source model generation.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    public function start(Mage_Sales_Model_Order $order)
    {
        // To avoid an infinite loop when calling order->save we set the registry key here.
        Mage::getModel('automaticinvoice/order_status')->saveToRegistry($order->getStatus(), true);

        if ($this->isEnabled($order) !== true) {
            $paymentMethod = Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')->getPaymentMethod($order);
            Mage::helper('automaticinvoice')->log('INFO: ' . ucfirst($this->getSourceType()) . ' generation is not active for current order status and payment method combination (' . $order->getStatus() . ', ' . $paymentMethod . '), skipping. Please check the configuration setting under System > Configuration > AutomaticInvoice > ' . ucfirst($this->getSourceType()) . '.', LOG_INFO);
            return false;
        }

        foreach ($this->getFilters() as $filterModel) {
            /** @var Vianetz_AutomaticInvoice_Model_Filter_FilterInterface $filterModel */
            if ($filterModel->isMatch($order, $this->getSourceType()) === false) {
                Mage::helper('automaticinvoice')->log('Filter ' . get_class($filterModel) . ' does not match for source type ' . ucfirst($this->getSourceType()));
                return false;
            }
        }

        Mage::helper('automaticinvoice')->log('Creating generate queue entry for source type ' . ucfirst($this->getSourceType()) . ' and order id #' . $order->getId());

        /** @var \Vianetz_AutomaticInvoice_Model_Queue_Message_Generate $queueMessage */
        $queueMessage = Mage::getModel('automaticinvoice/queue_message_generate')->setOrderId($order->getId())
            ->setSourceType($this->getSourceType());
        if ($this->isEmailEnabled($order) === true) {
            $queueMessage->setIsSendEmail(true);
        }
        if ($this->isNotifyEnabled($order) === true) {
            $queueMessage->setIsNotifyCustomer(true);
        }

        Mage::getSingleton('automaticinvoice/queue')->sendToQueue($queueMessage);

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
            ->isPaymentMethodAndOrderStatusActionEnabled('is_generate', $order, $this->getSourceType());
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    protected function isEmailEnabled(Mage_Sales_Model_Order $order)
    {
        return Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')
            ->isPaymentMethodAndOrderStatusActionEnabled('is_trigger_email', $order, $this->getSourceType());
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    protected function isNotifyEnabled(Mage_Sales_Model_Order $order)
    {
        return Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')
            ->isPaymentMethodAndOrderStatusActionEnabled('is_notify_customer', $order, $this->getSourceType());
    }

    /**
     * Return filters that should be applied to the order before generating the invoice/shipment.
     *
     * @return array
     */
    protected function getFilters()
    {
        return array(
            new Vianetz_AutomaticInvoice_Model_Filter_Paymentmethodorderstatus(),
            new Vianetz_AutomaticInvoice_Model_Filter_Grandtotal(),
            new Vianetz_AutomaticInvoice_Model_Filter_Producttype()
        );
    }
}