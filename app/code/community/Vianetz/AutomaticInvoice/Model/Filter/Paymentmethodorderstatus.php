<?php
/**
 * AutomaticInvoice Payment Method And Order Status Filter Class
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
class Vianetz_AutomaticInvoice_Model_Filter_Paymentmethodorderstatus implements Vianetz_AutomaticInvoice_Model_Filter_FilterInterface
{
    /**
     * Determines whether the current filter does match the order or not.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $sourceType
     *
     * @return boolean
     */
    public function isMatch(Mage_Sales_Model_Order $order, $sourceType)
    {
        if ($this->getPaymentMethodAndOrderStatusActions($order, $sourceType) === false) {
            Mage::helper('automaticinvoice')->log('Payment method "' . $this->getPaymentMethod($order) . '"" and order status "' . $order->getStatus() . '"" combination is not configured to trigger ' . $sourceType . ' generation for order #' . $order->getId());
            Mage::helper('automaticinvoice')->log('Configuration is: ' . Zend_Debug::dump($this->getAllowedPaymentMethodsAndOrderStatus($order, $sourceType), null, false));

            return false;
        }

        return true;
    }

    /**
     * @param $checkboxName
     * @param Mage_Sales_Model_Order $order
     * @param $sourceType
     *
     * @return boolean
     */
    public function isPaymentMethodAndOrderStatusActionEnabled($checkboxName, Mage_Sales_Model_Order $order, $sourceType)
    {
        $configuration = $this->getPaymentMethodAndOrderStatusActions($order, $sourceType);
        if (isset($configuration[$checkboxName]) === true
            && $configuration[$checkboxName] === Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox::VALUE_CHECKED) {
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    public function getPaymentMethod(Mage_Sales_Model_Order $order)
    {
        return $order->getPayment()->getMethodInstance()->getCode();
    }

    /**
     * Check if the payment method and status of the given order is in the configured payment methods to trigger the AutomaticInvoice extension.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $sourceType
     *
     * @return boolean|array
     */
    private function getPaymentMethodAndOrderStatusActions(Mage_Sales_Model_Order $order, $sourceType)
    {
        foreach ($this->getPaymentMethodsAndOrderStatusConfiguration($order->getStore(), $sourceType) as $allowedCombinationData) {
            if ($this->getPaymentMethod($order) === $allowedCombinationData['payment_method']
             && $order->getStatus() === $allowedCombinationData['order_status']) {
                return $allowedCombinationData;
            }
        }

        return false;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @param string $sourceType
     *
     * @return array
     */
    private function getPaymentMethodsAndOrderStatusConfiguration(Mage_Core_Model_Store $store, $sourceType)
    {
        $paymentMethodsAndOrderStatus = Mage::getStoreConfig('automaticinvoice/' . $sourceType . '/trigger_payment_methods_order_status_email', $store);
        if (empty($paymentMethodsAndOrderStatus) === true) {
            return array();
        }

        $paymentMethodsAndOrderStatus = unserialize($paymentMethodsAndOrderStatus);
        if (is_array($paymentMethodsAndOrderStatus) === false) {
            Mage::helper('automaticinvoice')->log('Error while unserializing configured payment methods and order status. Skipping this filter.');
            return array();
        }

        return $paymentMethodsAndOrderStatus;
    }
}