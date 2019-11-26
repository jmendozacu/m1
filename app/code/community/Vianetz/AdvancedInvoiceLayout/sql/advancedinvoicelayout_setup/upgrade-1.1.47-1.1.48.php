<?php
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$entitiesToAlter = array('customer/customer_group');

foreach ($entitiesToAlter as $entity) {
    $installer->getConnection()
        ->addColumn($installer->getTable($entity), 'vianetz_advancedinvoicelayout_customer_group_freetext', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 500,
            'nullable'  => true,
            'comment'   => 'AdvancedInvoiceLayout Customer Group Specific Free Text'
        ));
}
$installer->endSetup();
