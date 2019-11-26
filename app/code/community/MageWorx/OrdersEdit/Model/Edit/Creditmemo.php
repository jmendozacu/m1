<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Edit_Creditmemo extends Mage_Core_Model_Abstract
{
    /**
     * List of totals which can be changed in order
     * @var array
     */
    protected $_availableTotals = array(
        'shipping_tax_amount',
        'base_shipping_tax_amount',
        'base_shipping_tax_amount',
        'subtotal',
        'base_subtotal',
        'subtotal_incl_tax',
        'base_subtotal_incl_tax',
        'grand_total',
        'base_grand_total',
        'tax_amount',
        'base_tax_amount',
        'discount_amount',
        'base_discount_amount',
        'shipping_amount',
        'base_shipping_amount',
        'shipping_incl_tax',
        'base_shipping_incl_tax',
        'hidden_tax_amount',
        'base_hidden_tax_amount'
    );

    /**
     * Array of order products to be refunded
     *
     * @var array
     */
    protected $_itemsToRefund = array();

    /**
     * Create creditmemo for order changes
     *
     * @param Mage_Sales_Model_Order $origOrder
     * @param Mage_Sales_Model_Order $newOrder
     * @param bool $dummy - create dummy credit memo without totals
     * @return $this
     */
    public function refundChanges(Mage_Sales_Model_Order $origOrder, Mage_Sales_Model_Order $newOrder, $dummy = false)
    {
        $cmData = array();
        $cmData['qtys'] = $this->getItemsToRefund();

        /** @var Mage_Sales_Model_Service_Order $orderService */
        $orderService = Mage::getModel('sales/service_order', $newOrder);
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $orderService->prepareCreditmemo($cmData);

        if (!$dummy) {
            foreach ($this->_availableTotals as $code) {
                $diff = $origOrder->getData($code) - $newOrder->getData($code);
                if (!$diff) {
                    continue;
                }

                $creditmemo->setData($code, $diff);
            }
        } else {
            foreach ($this->_availableTotals as $code) {
                $creditmemo->setData($code, 0);
            }
        }

        // Return refunded items to stock
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $creditmemoItem->setBackToStock(true);
        }

        // Do not create empty credit memo without any item
        if (count($creditmemo->getAllItems()) === 0 && $creditmemo->getBaseGrandTotal() <= 0) {
            return $this;
        }

        $creditmemo->register();
        $creditmemo->save();

        $creditmemo->getOrder()->save();

        return $this;
    }

    /**
     * Add order item to be refunded
     *
     * @param $itemId
     * @param $qty
     * @return $this
     */
    public function addItemToRefund($itemId, $qty)
    {
        $this->_itemsToRefund[$itemId] = $qty;

        return $this;
    }

    /**
     * Get all the order items to be refunded
     *
     * @return array
     */
    public function getItemsToRefund()
    {
        // To prevent creditmemo from adding items;
        if (!count($this->_itemsToRefund)) {
            return array(0 => 0);
        }

        return $this->_itemsToRefund;
    }
}
