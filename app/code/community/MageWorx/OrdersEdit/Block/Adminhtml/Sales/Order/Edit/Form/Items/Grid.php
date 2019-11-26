<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('productGrid');

        $this->setRowClickCallback('orderEditItems.productGridRowClick.bind(orderEditItems)');
        $this->setCheckboxCheckCallback('orderEditItems.productGridCheckboxCheck.bind(orderEditItems)');
        $this->setRowInitCallback('orderEditItems.productGridRowInit.bind(orderEditItems)');
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection()
    {
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->addAttributeToSelect('gift_message_available');

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    /**
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumnAfter(
            'type',
            array(
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ), 'name'
        );

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $order = $this->getData('order');
        return Mage::app()->getStore($order->getStoreId());
    }
}