<?php
/**
 * AdvancedInvoiceLayout PDF item model
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
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Item_Default
 * @method Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item getItem()
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Default extends Mage_Core_Model_Abstract
{
    /**
     * Get order model for current item.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getItem()->getOrderItem()->getOrder();
    }

    /**
     * Return product instance.
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getItemProduct() instanceof Mage_Catalog_Model_Product
            || $this->getItemProduct()->getId() != $this->getItem()->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getItem()->getProductId());
            $this->setItemProduct($product);
        }

        return $this->getItemProduct();
    }

    /**
     * Retrieve item options.
     * @see Mage_Sales_Model_Order_Pdf_Items_Abstract::getItemOptions()
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Return item price for specified item.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @param integer|null $taxDisplayType the tax display type, if null the configuration value is taken into account
     *                     (this can be one of Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX, Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX, null)
     *
     * @return float
     */
    public function getItemPrice($item, $taxDisplayType = null)
    {
        $orderItem = $item->getOrderItem();
        $store = null;
        if (empty($orderItem) === false) {
            $store = $item->getOrderItem()->getStore();
        }

        if ($taxDisplayType === null) {
            $taxDisplayType = Mage::getStoreConfig('advancedinvoicelayout/general/tax_display_type', $store);
        }

        if ($taxDisplayType != Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX) {
            if ($item->getPriceInclTax() != $item->getPrice() && $item->getPriceInclTax() > 0) {
                $price = round($item->getPriceInclTax(), 2);
            } else {
                $price = round($item->getPrice(), 2);
            }
        } else {
            $price = round($item->getPrice(), 2);
        }

        return $price;
    }

    /**
     * Return item tax percent value.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemTaxPercent($item)
    {
        return (float)$item->getTaxPercent();
    }

    /**
     * Return item row total.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemRowTotal($item, $taxDisplayType = null)
    {
        $orderItem = $item->getOrderItem();
        $store = null;
        if (empty($orderItem) === false) {
            $store = $item->getOrderItem()->getStore();
        }

        if ($taxDisplayType === null) {
            $taxDisplayType = Mage::getStoreConfig('advancedinvoicelayout/general/tax_display_type', $store);
        }
        
        if ($taxDisplayType != Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX) {
            if ($item->getPriceInclTax() != $item->getPrice()
                && $item->getPriceInclTax() > 0) {
                $rowTotal = round($item->getRowTotalInclTax(), 2);
            } else {
                $rowTotal = round($item->getPrice() * $item->getQty(), 2);
            }
        } else {
            $rowTotal = round($item->getRowTotal(), 2);
        }

        return $rowTotal;
    }

    /**
     * Return item discount amount.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemDiscountAmount($item)
    {
        return $item->getDiscountAmount();
    }

    /**
     * @param $item
     * @param $taxDisplayType
     *
     * @return float
     */
    public function getItemRowTotalWithDiscountAmount($item, $taxDisplayType)
    {
        return $this->getItemRowTotal($item, $taxDisplayType) - $this->getItemDiscountAmount($item);
    }

    /**
     * Get additional product attributes to display in pdf document.
     *
     * @return array
     */
    public function getProductAdditionalAttributes()
    {
        $productAttributesToDisplay = array();

        $customProductAttributes = explode(',', Mage::getStoreConfig('advancedinvoicelayout/invoice/show_additional_item_attributes', $this->getOrder()->getStore()));
        foreach ($customProductAttributes as $attributeCode) {
            $attribute = $this->getProduct()->getResource()->getAttribute($attributeCode);
            if (!$attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
                continue;
            }
            $attributeLabelToDisplay = Mage::helper('advancedinvoicelayout')->__($attribute->getFrontendLabel());
            switch ($attribute->getFrontendInput()) {
                case 'select':
                    $attributeValueToDisplay = $this->getProduct()->getAttributeText($attributeCode);
                    break;
                case 'multiselect':
                    $attributeValueToDisplay = $this->getProduct()->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($this->getProduct());
                    break;
                default:
                    $attributeValueToDisplay = $this->getProduct()->getData($attributeCode);
            }
            if (empty($attributeValueToDisplay) === true) {
                continue;
            }

            $productAttributesToDisplay[] = array(
                'label' => $attributeLabelToDisplay,
                'value' => $attributeValueToDisplay
            );
        }

        return $productAttributesToDisplay;
    }

    /**
     * Check if current item can be downloaded (is of product type Mage_Downloadable).
     *
     * @return boolean
     */
    public function isDownloadable()
    {
        return ($this->getItem()->getOrderItem()->getProductType() === Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
    }

    /**
     * Check if given item is of type bundle or not.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return bool
     */
    public function isItemBundle($item)
    {
        return ($item->getOrderItem()->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
    }
}
