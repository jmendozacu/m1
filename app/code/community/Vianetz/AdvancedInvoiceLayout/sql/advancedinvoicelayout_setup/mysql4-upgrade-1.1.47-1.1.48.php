<?php
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$entitiesToAlter = array('customer/customer_group');

foreach ($entitiesToAlter as $entity) {
    $installer->getConnection()->addColumn($installer->getTable($entity), 'vianetz_advancedinvoicelayout_customer_group_freetext', 'varchar(255) default NULL');
}
$installer->endSetup();
