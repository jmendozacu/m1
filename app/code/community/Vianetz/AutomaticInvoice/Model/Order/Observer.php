<?php
/**
 * AutomaticInvoice Observer Class
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
class Vianetz_AutomaticInvoice_Model_Order_Observer
{
    /**
     * Generate invoice automatically based on several config values.
     *
     * Event: automaticinvoice_sales_order_status_change_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function generateInvoice(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();

        Mage::getModel('automaticinvoice/workflow_order_create_invoice')->start($order);

        return $this;
    }

    /**
     * Generate shipment automatically based on several config values.
     *
     * Event: sales_order_save_after
     * This event also assures that the correct store id for the getStoreConfig() calls is used.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function generateShipment(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();

        Mage::getModel('automaticinvoice/workflow_order_create_shipment')->start($order);

        return $this;
    }

    /**
     * Save invoice pdf to file system.
     *
     * Event: sales_order_invoice_save_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function saveInvoicePdf(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $event->getInvoice();

        Mage::getModel('automaticinvoice/workflow_order_save_invoice')->start($invoice);

        return $this;
    }

    /**
     * Save shipment pdf to file system.
     *
     * Event: sales_order_shipment_save_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function saveShipmentPdf(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $event->getShipment();

        Mage::getModel('automaticinvoice/workflow_order_save_shipment')->start($shipment);

        return $this;
    }

    /**
     * Send invoice email to customer if it has not been sent yet.
     * This method is used in case that the payment module generates the invoice but does not send the email.
     *
     * Event: automaticinvoice_sales_order_status_change_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function sendInvoiceEmail(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        foreach ($order->getInvoiceCollection() as $invoice) {
            try {
                // Reload invoice model to get all data (as otherwise there may be problems e.g. with Billsafe invoice not showing bank account details).
                $invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());

                Mage::getModel('automaticinvoice/workflow_order_sendEmail_invoice')->start($invoice);
                $invoice->save();
            } catch (Exception $ex) {
                $this->_getHelper()->log('Error occurred while sending invoice email for invoice #' . $invoice->getIncrementId() . ': ' . $ex->getMessage() . $ex->getTraceAsString());
            }
        }

        return $this;
    }

    /**
     * Save order status to registry to determine order status changes.
     *
     * Event: sales_order_load_after
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function registerOrderStatus(Varien_Event_Observer $observer)
    {
        try {
            Mage::getModel('automaticinvoice/order_status')->saveToRegistry($observer->getOrder()->getStatus());
        } catch (Exception $exception) {
            $this->_getHelper()->log('Cannot register order status in registry: ' . $exception->getMessage());
        }

        return $this;
    }

    /**
     * Dispatch a new event if order status did change.
     *
     * Event: sales_order_save_after
     *
     * To determine an order status change we save the order state in the registry in registerOrderStatus() and compare it in here
     * as there is no build-in Magento event by default for this case.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function dispatchOrderStatusChangeEvent(Varien_Event_Observer $observer)
    {
        try {
            $previousOrderStatus = Mage::getModel('automaticinvoice/order_status')->getFromRegistry();
            // in case that a new order is generated the previous state is null/empty.
        } catch (Exception $exception) {
            $this->_getHelper()->log('Cannot retrieve order status from registry: ' . $exception->getMessage());
            $previousOrderStatus = $observer->getOrder()->getStatus();
        }

        if ($previousOrderStatus != $observer->getOrder()->getStatus()) {
            $this->_getHelper()->log('Order #' . $observer->getOrder()->getId() . '/' . $observer->getOrder()->getIncrementId() . ' status changed from ' . $previousOrderStatus . ' to ' . $observer->getOrder()->getStatus());
            // Dispatch a new event.
            Mage::dispatchEvent(
                'automaticinvoice_sales_order_status_change_after',
                array(
                    'order' => $observer->getOrder(),
                    'previousStatus' => $previousOrderStatus,
                    'currentStatus' => $observer->getOrder()->getStatus(),
                    'store' => Mage::app()->getStore())
            );
        }

        return $this;
    }

    /**
     * Sets the notify customer registry key if we are in admin depending on the "Notify Customer" checkbox on the order create form.
     *
     * Event: adminhtml_sales_order_create_process_data
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function setNotifyCustomerRegistryKey(Varien_Event_Observer $observer)
    {
        $requestPostDataArray = $observer->getEvent()->getRequest();

        if (isset($requestPostDataArray['order']['send_confirmation']) === false) {
            $isNotifyCustomer = false;
        } else {
            $isNotifyCustomer = (bool)$requestPostDataArray['order']['send_confirmation'];
        }

        Mage::register(Vianetz_AutomaticInvoice_Model_Action_Notify::REGISTRY_KEY_NOTIFY_CUSTOMER, $isNotifyCustomer);

        if ($isNotifyCustomer === true) {
            $this->_getHelper()->log('Setting registry key for notifying customer to true.');
        } else {
            $this->_getHelper()->log('Setting registry key for notifying customer to false.');
        }

        return $this;
    }

    /**
     * @return Vianetz_AutomaticInvoice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('automaticinvoice');
    }
}