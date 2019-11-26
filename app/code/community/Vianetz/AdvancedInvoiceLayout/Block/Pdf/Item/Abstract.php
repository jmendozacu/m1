<?php
/**
 * AdvancedInvoiceLayout Pdf item abstract block
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
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Abstract
 * @method Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item getItem()
 */
abstract class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Abstract extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract
{
    /**
     * @var Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Default
     */
    protected $_itemContainerModel;

    /**
     * Default constructor initializes item model.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->initializeItemContainerModel();
    }

    /**
     * Get order model for current item.
     * 
     * @api
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getItem()->getOrderItem()->getOrder();
    }

    /**
     * Retrieve item options.
     * @see Mage_Sales_Model_Order_Pdf_Items_Abstract::getItemOptions()
     *
     * @api
     *
     * @return array
     */
    public function getItemOptions()
    {
        return $this->_itemContainerModel->getItemOptions();
    }

    /**
     * Get item qty.
     *
     * @api
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemQty($item)
    {
        if ($this->_itemContainerModel->isItemBundle($item) === true) {
            return '';
        }

        return $item->getQty()*1;
    }

    /**
     * Return product instance.
     *
     * @api
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_itemContainerModel->getProduct();
    }

    /**
     * Return item price.
     *
     * @api
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return string
     */
    public function getItemPrice($item)
    {
        return $this->_itemContainerModel->getItemPrice($item);
    }

    /**
     * Return item row total.
     *
     * @api
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return string
     */
    public function getItemRowTotal($item)
    {
        return $this->_itemContainerModel->getItemRowTotal($item);
    }

    /**
     * Get item tax value.
     * 
     * @api
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @return float
     */
    public function getItemTax($item)
    {
        if ($this->isShowTaxes() === false) {
            return 0;
        }

        return $item->getTaxAmount();
    }

    /**
     * Get item tax percent string.
     *
     * @api
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     *
     * @since Magento 1.7.x
     *
     * @return string
     */
    public function getItemTaxPercent($item)
    {
        if ($this->isShowTaxes() === true) {
            return $this->helper('advancedinvoicelayout')->__('%g%%', $this->_itemContainerModel->getItemTaxPercent($item->getOrderItem()));
        }

        return '';
    }

    /**
     * Get short description for product.
     *
     * @api
     *
     * @return string
     */
    public function getProductShortDescription()
    {
        $isShowShortDescription = Mage::getStoreConfigFlag($this->_getBlockConfigPath('show_product_shortdescription'), $this->getOrder()->getStore());
        if ($isShowShortDescription === false) {
            return '';
        }

        $shortDescription = strip_tags(htmlspecialchars_decode($this->getProduct()->getShortDescription()));
        return $shortDescription;
    }

    /**
     * Get additional product attributes to display in pdf document.
     *
     * @api
     *
     * @return array
     */
    public function getProductAdditionalAttributes()
    {
        return $this->_itemContainerModel->getProductAdditionalAttributes();
    }

    /**
     * Return relative product image URL.
     *
     * @api
     *
     * @return string
     */
    public function getProductImagePath()
    {
        $isImagePrintingEnabled = Mage::getStoreConfigFlag($this->_getBlockConfigPath('show_product_image'), $this->getOrder()->getStore());
        $imageUrl = $this->getProductImage();
        if (empty($imageUrl) === true || $isImagePrintingEnabled === false) {
            return '';
        }

        return $this->helper('vianetz_core/file')->getRelativeMediaPath($imageUrl);
    }

    /**
     * Check if product item image exists and is readable.
     *
     * @api
     *
     * @return boolean
     */
    public function isProductImagePath()
    {
        try {
            $productImagePath = $this->getProductImagePath();
        } catch (Exception $ex) {
            return false;
        }

        return (empty($productImagePath) === false && is_file($productImagePath) === true);
    }

    /**
     * Return Purchased links for order item (if downloadable product).
     *
     * @see Mage_Downloadable_Model_Sales_Order_Pdf_Items_Abstract::getLinks()
     *
     * @api
     *
     * @return array
     */
    public function getDownloadLinks()
    {
        return $this->_itemContainerModel->getDownloadLinks();
    }

    /**
     * Check if current item can be downloaded (is of product type Mage_Downloadable).
     *
     * @api
     *
     * @return boolean
     */
    public function isDownloadable()
    {
        return $this->_itemContainerModel->isDownloadable();
    }

    /**
     * Return all bundle items.
     *
     * @see Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract::getChilds() for assoziative array format
     *
     * @api
     *
     * @return array
     */
    public function getBundleItems()
    {
        return $this->_itemContainerModel->getBundleItems();
    }

    /**
     * Return option value.
     *
     * @api
     *
     * @param array $option
     *
     * @return string
     */
    public function getOptionValue(array $option)
    {
        if (isset($option['print_value']) === true) {
            return $option['print_value'];
        }

        return $option['value'];
    }

    /**
     * Unfortunately due to a bug in the dompdf library table cells do not recognize word-wrap: break-word so we have
     * to manually break the text into smaller parts to circumvent large table cells.
     *
     * @api
     *
     * @param string $text The text to format
     * @param integer $stringPartLength The length/width of the splitted text string.
     *
     * @return string The converted html string for the item sku.
     */
    public function formatWithLineBreak($text, $stringPartLength = 15)
    {
        $html = '';
        for ($i = 0; $i < strlen($text); $i++) {
            if ($i !== 0 && ($i % $stringPartLength) === 0) {
                $html .= '<br />';
            }
            $html .= $text[$i];
        }

        return $html;
    }

    /**
     * Initialize the item container model depending on the product type.
     *
     * @return Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Abstract
     */
    private function initializeItemContainerModel()
    {
        $this->_itemContainerModel = Mage::getModel('advancedinvoicelayout/pdf_container_item_default')
            ->setItem($this->getItem());

        if ($this->_itemContainerModel->isDownloadable() === true) {
            $this->_itemContainerModel = Mage::getModel('advancedinvoicelayout/pdf_container_item_downloadable');
        } elseif ($this->_itemContainerModel->isItemBundle($this->getItem()) === true) {
            $this->_itemContainerModel = Mage::getModel('advancedinvoicelayout/pdf_container_item_bundle');
        }

        $this->_itemContainerModel->setItem($this->getItem());

        return $this;
    }

    /**
     * @return string
     */
    private function getProductImage()
    {
        return Mage::helper('catalog/image')->init($this->getProduct(), 'image')->resize(265);
    }
}
