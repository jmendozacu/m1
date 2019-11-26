<?php
/**
 * AdvancedInvoiceLayout PDF crosssells model
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
 * @package     Vianetz\AdvancedInvoiceLayout
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
* @copyright   Copyright (c) 2006-18 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */

/**
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Bundle
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Bundle extends Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Default
{
    /**
     * Return item price only if row total greater than zero.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @param integer|null $taxDisplayType the tax display type, if null the configuration value is taken into account
     *
     * @return float|null
     */
    public function getItemPrice($item, $taxDisplayType = null)
    {
        if ($this->getItemRowTotal($item) == 0) {
            return null;
        }

        return parent::getItemPrice($item, $taxDisplayType);
    }

    /**
     * Return item row total.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemRowTotal($item)
    {
        $rowTotal = parent::getItemRowTotal($item);

        if ($rowTotal == 0) {
            return null;
        }

        return $rowTotal;
    }

    /**
     * Return all bundle items.
     *
     * @see Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract::getChilds() for assoziative array format
     *
     * @return array
     */
    public function getBundleItems()
    {
        $productType = $this->getItem()->getOrderItem()->getProductType();
        if ($productType !== Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return array();
        }

        $items = array();
        if ($this->getItem() instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $items = $this->getItem()->getInvoice()->getAllItems();
        } elseif ($this->getItem() instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $items = $this->getItem()->getShipment()->getAllItems();
        } elseif ($this->getItem() instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $items = $this->getItem()->getCreditmemo()->getAllItems();
        }

        $bundleItems = array();
        foreach ($items as $item) {
            $parentItem = $item->getOrderItem()->getParentItem();
            if (empty($parentItem) === false) {
                $bundleItems[$parentItem->getId()][$item->getOrderItemId()] = $item;
            }
        }

        if (isset($bundleItems[$this->getItem()->getOrderItem()->getId()]) === false) {
            return array();
        }

        return $bundleItems[$this->getItem()->getOrderItem()->getId()];
    }
}