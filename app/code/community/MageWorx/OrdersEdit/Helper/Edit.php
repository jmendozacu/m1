<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Helper_Edit extends Mage_Core_Helper_Abstract
{
    protected $_availableBlocks = null;

    /**
     * Get blocks of order that can be edited
     *
     * @return array|null
     */
    public function getAvailableBlocks()
    {
        if (is_null($this->_availableBlocks)){
            $this->_availableBlocks = array(
                array(
                    'className' => 'head-general',
                    'blockId' => 'order_info',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_general',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_general'
                ),
                array(
                    'className' => 'head-account',
                    'blockId' => 'customer_info',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_customer',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_customer'
                ),
                array(
                    'className' => 'head-billing-address',
                    'blockId' => 'billing_address',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_address',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_address'
                ),
                array(
                    'className' => 'head-shipping-address',
                    'blockId' => 'shipping_address',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_address',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_address'
                ),
                array(
                    'className' => 'head-payment-method',
                    'blockId' => 'payment_method',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_payment',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_payment'
                ),
                array(
                    'className' => 'head-shipping-method',
                    'blockId' => 'shipping_method',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_shipping',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_shipping'
                ),
                array(
                    'className' => 'head-products',
                    'blockId' => 'order_items',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_items',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_items'
                ),
                array(
                    'className' => 'head-coupons',
                    'blockId' => 'sales_order_coupons',
                    'block' => 'mageworx_ordersedit/adminhtml_sales_order_edit_form_coupons',
                    'changedBlock' => 'mageworx_ordersedit/adminhtml_sales_order_changed_coupons'
                ),
            );
        }

        return $this->_availableBlocks;
    }

    /**
     * Get block to edit by its id
     *
     * @param $blockId
     * @return array
     */
    public function getBlockDataById($blockId)
    {
        foreach ($this->getAvailableBlocks() as $block) {
            if ($block['blockId'] == $blockId) {
                return $block;
            }
        }

        return array();
    }

    /**
     * Get url template to load edit form
     *
     * @return mixed
     */
    public function getEditUrlTemplate()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/loadEditForm', array('block_id' => '%block_id%', 'order_id' => $this->getOrderId()));
    }

    /**
     * Get url to load customers grid
     *
     * @return mixed
     */
    public function getCustomersGridUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/customersGrid');
    }

    /**
     * Get url template to submit selected customer
     *
     * @return mixed
     */
    public function getSubmitCustomerUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/submitCustomer', array('id' => '%customer_id%'));
    }

    /**
     * Get url to load products grid
     *
     * @return mixed
     */
    public function getProductGridUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/productGrid', array('order_id' => $this->getOrderId()));
    }

    /**
     * Get url to apply order changes
     *
     * @return mixed
     */
    public function getApplyChangesUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/applyChanges', array('order_id' => $this->getOrderId(), 'edited_block' => '%edited_block%'));
    }

    /**
     * Get url to save order changes
     *
     * @return mixed
     */
    public function getSaveChangesUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/saveOrder', array('order_id' => $this->getOrderId()));
    }

    /**
     * Get url to cancel order changes
     * (unset all changes data)
     *
     * @return mixed
     */
    public function getCancelChangesUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/mageworx_ordersedit_edit/cancelChanges', array('order_id' => $this->getOrderId()));
    }

    /**
     * @param $orderId
     * @return string
     */
    public function getPendingChangesKey($orderId)
    {
        return 'ordersedit_edit_changes_' . $orderId;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getPendingChanges($orderId)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $sessionKey = $this->getPendingChangesKey($orderId);

        $changes = $session->getData($sessionKey);
        if (!$changes) {
            $changes = array();
        }

        return $changes;
    }

    /**
     * @param $orderId
     * @param $newChanges
     * @return array
     */
    public function addPendingChanges($orderId, $newChanges)
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('adminhtml/session');
        $sessionKey = $this->getPendingChangesKey($orderId);

        $oldChanges = $this->getPendingChanges($orderId);

        if (is_null($oldChanges)) {
            $oldChanges = array();
        }

        if (isset($newChanges['quote_items']) && !empty($newChanges['quote_items'])) {
            foreach($newChanges['quote_items'] as $quoteId => $quoteItemData) {
                if (isset($quoteItemData['configured']) && $quoteItemData['configured']) {
                    $quoteItem = Mage::getModel('sales/quote_item')->load($quoteId);
                    $productId = $quoteItem->getProductId();
                    unset($quoteItemData['configured']);
                    if (isset($quoteItemData['action'])) {
                        unset($quoteItemData['action']);
                    }

                    $quoteItemData['product_id'] = $productId;
                    $newChanges['product_to_add'][$quoteItem->getItemId()] = $quoteItemData;
                }
            }

            if (isset($oldChanges['quote_items']) && !empty($oldChanges['quote_items'])) {
                $quoteItems  = $newChanges['quote_items'] + $oldChanges['quote_items'];
                $newChanges['quote_items'] = $quoteItems;
            }
        }

        $changes = array_merge($oldChanges, $newChanges);

        $session->setData($sessionKey, $changes);

        return $changes;
    }

    /**
     * @param $orderId
     */
    public function resetPendingChanges($orderId)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $sessionKey = $this->getPendingChangesKey($orderId);

        $session->unsetData($sessionKey);
    }

    /**
     * Get current order entity id
     *
     * @return mixed
     */
    protected function getOrderId()
    {
        $order = Mage::registry('current_order');
        if (isset($order))
        {
            $orderId = $order->getId();
        } else {
            $orderId = null;
        }

        return $orderId;
    }

    /**
     * Remove all quote items for the order with the flag is_temporary = 1
     *
     * @param $order
     */
    public function removeTempQuoteItems($order)
    {
        $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);
        $quoteItem = Mage::getModel('sales/quote_item');
        $quoteItemsCollection = $quoteItem->getCollection()->setQuote($quote)->addFilter('ordersedit_is_temporary', 1);
        foreach ($quoteItemsCollection as $item) {
            $item->delete();
        }
    }

    /**
     * Check quote item.
     *
     * @param Mage_Sales_Model_Quote_Item $quoteItem | null
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return bool
     */
    public function isQuoteItemAvailable(Mage_Sales_Model_Quote_Item $quoteItem, Mage_Sales_Model_Order_Item $orderItem)
    {
        if ($quoteItem->getId()) {
            return true;
        } // Temporary fix for restoration of quote items with deleted products (just ignore them)
        elseif (!Mage::getModel('catalog/product')->load($orderItem->getProductId())->getId()) {
            return true;
        } // Temporary fix for bundle products (qoute item restoration)
        elseif ($orderItem->getProductType() == 'bundle') {
            return true;
        } // Temporary fix for bundle products (qoute item restoration)
        elseif ($orderItem->getParentItem() && $orderItem->getParentItem()->getProductType() == 'bundle') {
            return true;
        }
        elseif (!($orderItem->getQtyToCancel() || $orderItem->getQtyToRefund())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check order item for cancel or refund possibility (Magento version < 1.8 doesn't work with bundle products properly).
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return bool
     */
    public function checkOrderItemForCancelRefund(Mage_Sales_Model_Order_Item $orderItem)
    {
        $salesVersion = Mage::getConfig()->getModuleConfig("Mage_Sales")->version;
        if (version_compare($salesVersion, '1.6.0.8', '>=') ||
            (
                version_compare($salesVersion, '1.6.0.8', '<') &&
                $orderItem->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
            )
        ){
            if (!$orderItem->getQtyToRefund() && !$orderItem->getQtyToCancel() && $orderItem->getQtyToInvoice()) {
                return true;
            } elseif (!($orderItem->getQtyToCancel() || $orderItem->getQtyToRefund())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function isOrderEditable(Mage_Sales_Model_Order $order)
    {
        return Mage::helper('mageworx_ordersedit')->isOrderEditable($order);
    }

    /**
     * Get rest of available item qty.
     * qty = qty - canceled [ - refunded ] (optional)
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param bool|false $excludeRefunded
     * @return float|int
     */
    public function getOrderItemQtyRest(Mage_Sales_Model_Order_Item $item, $excludeRefunded = false)
    {
        $qty = $item->getOrigData('qty_ordered') - $item->getQtyCanceled();
        if ($excludeRefunded) {
            $qty -= $item->getQtyRefunded();
        }

        return $qty;
    }

    /**
     * Remove refunded items from the quote items collection
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Order $order
     * @return void
     */
    public function removeRefundedItemsFromQuote(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Sales_Model_Resource_Quote_Item_Collection $quoteItemsCollection */
        $quoteItemsCollection = $quote->getItemsCollection();

        try {
            /** @var Mage_Sales_Model_Quote_Item $quoteItem */
            foreach ($quoteItemsCollection as $quoteItem) {
                $orderItem = $order->getItemByQuoteItemId($quoteItem->getId());
                $quoteItem->setHasError(false);

                if (!$orderItem) {
                    continue;
                }

                if ($this->getOrderItemQtyRest($orderItem, true) <= 0) {
                    $quoteItemsCollection->removeItemByKey($quoteItem->getId());
                }
            }
        } catch (Exception $e) {
            // When the APO was uninstalled (or disabled) the items with a custom options (APO) throws an errors
            Mage::logException($e);
        }
    }
}
