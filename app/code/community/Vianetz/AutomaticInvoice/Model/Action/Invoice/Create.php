<?php
/**
 * AutomaticInvoice Invoice Create Action Class
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
class Vianetz_AutomaticInvoice_Model_Action_Invoice_Create extends Vianetz_AutomaticInvoice_Model_Action_Abstract
{
    /**
     * Check whether the action is allowed to be executed or not.
     *
     * @return boolean
     */
    public function canRun()
    {
        if ($this->order->canInvoice() === false) {
            Mage::helper('automaticinvoice')->log('Order #' . $this->order->getId() . ' cannot be invoiced.', LOG_ERR);
            return false;
        }

        return true;
    }

    /**
     * Generate invoice automatically based on several config values.
     *
     * @return Vianetz_AutomaticInvoice_Model_Action_Invoice_Create
     */
    public function run()
    {
        if ($this->canRun() === false) {
            return $this;
        }

        $store = $this->order->getStore();
        $invoice = $this->order->prepareInvoice($this->_getItemsToInvoice($this->order));

        $invoice->setIsPaid(false);

        $captureCase = Mage::getStoreConfig('automaticinvoice/invoice/capture_case', $store);
        if (empty($captureCase) === false) {
            Mage::helper('automaticinvoice')->log('Requested Capture Case: ' . $captureCase);
            $invoice->setRequestedCaptureCase($captureCase);
        }

        Mage::helper('automaticinvoice')->log('Registering invoice for order #' . $this->order->getId());
        $invoice->register();
        $this->order->setIsInProcess(true);

        $isAddOrderComment = Mage::getStoreConfigFlag('automaticinvoice/invoice/comment', $store);
        if ($isAddOrderComment === true) {
            foreach ($this->order->getStatusHistoryCollection() as $status) {
                if (!$status->isDeleted() && $status->getComment()) {
                    Mage::helper('automaticinvoice')->log('Adding order comment to invoice #' . $invoice->getId());
                    $invoice->addComment($status->getComment());
                }
            }
        }

        Mage::helper('automaticinvoice')->log('Saving invoice for order #' . $this->order->getId());
        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        $this->order->addRelatedObject($invoice);

        // Set order status.
        Mage::helper('automaticinvoice')->log('Set order state for order #' . $this->order->getIncrementId() . ' to ' . Mage::getStoreConfig('automaticinvoice/invoice/new_order_status', $store));
        $this->order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING,
            Mage::getStoreConfig('automaticinvoice/invoice/new_order_status', $store),
            Mage::helper('automaticinvoice')->__('Invoice #' . $invoice->getId() . ' automatically generated.')
        );
        $this->order->save();

        Mage::helper('automaticinvoice')->log('Invoice #' . $invoice->getId() . ' has been generated successfully.', LOG_INFO);

        $this->result = $invoice;

        return $this;
    }

    /**
     * Return items that can be invoiced with their quantity.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     * @throws Vianetz_AutomaticInvoice_Exception
     */
    protected function _getItemsToInvoice(Mage_Sales_Model_Order $order)
    {
        $itemsToInvoice = array();

        $isInvoiceOnlyInStockProducts = Mage::getStoreConfigFlag('automaticinvoice/invoice/limit_to_in_stock_items', $order->getStoreId());
        if ($isInvoiceOnlyInStockProducts === true) {
            $itemsToInvoice = $this->_getInStockItemArray($order);
            Mage::helper('automaticinvoice')->log('Invoicing only in-stock items: ' . join(',', array_keys($itemsToInvoice)));
            if (empty($itemsToInvoice) === true) {
                // No items in stock, skipping..
                throw new Vianetz_AutomaticInvoice_Exception('No in-stock items to invoice, skipping..');
            }
        }

        return $itemsToInvoice;
    }

    /**
     * Return item => qty array that can be passed to prepareInvoice() call.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    protected function _getInStockItemArray(Mage_Sales_Model_Order $order)
    {
        $itemIdArray = array();
        foreach ($order->getAllItems() as $item) {
            $itemQtyInStock = $this->_getItemQtyInStock($item);
            if ($itemQtyInStock > 0) {
                $itemIdArray[$item->getId()] = $itemQtyInStock;
            }
        }

        return $itemIdArray;
    }

    /**
     * Retrieve in stock qty of the specified order item.
     *
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return float
     */
    protected function _getItemQtyInStock(Mage_Sales_Model_Order_Item $item)
    {
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

        if ($stockItem->getManageStock() == false) {
            return $item->getQtyOrdered();
        }

        return min(max($stockItem->getQty() - $stockItem->getMinQty(), 0), $item->getQtyOrdered());
    }
}