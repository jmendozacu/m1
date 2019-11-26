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
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Crosssells
 * @method Mage_Sales_Model_Abstract getSource()
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Crosssells extends Mage_Core_Model_Abstract
{
    /**
     * Get crosssell products collection.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    public function getProductCollection()
    {
        $isCrosssellProductsEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/invoice/show_crosssell_products');
        if ($isCrosssellProductsEnabled === false) {
            return array();
        }

        $productIds = array();
        foreach ($this->getSource()->getAllItems() as $product) {
            $productIds[] = $product->getProductId();
        }
        $lastAdded = (int)array_pop($productIds);
        $collection = Mage::getModel('catalog/product_link')->useCrossSellLinks()
            ->getProductCollection()
            ->setStoreId($this->getSource()->getOrder()->getStoreId())
            ->addStoreFilter()
            ->setPageSize(3)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite();

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        $collection->addProductFilter($lastAdded);
        if (!empty($productIds)) {
            $collection->addExcludeProductFilter($productIds);
        }

        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $collection->load();

        return $collection;
    }
}
