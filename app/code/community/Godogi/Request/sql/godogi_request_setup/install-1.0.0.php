<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('godogi_request/request'))
    ->addColumn(
        'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Unique Identifier'
    )
    ->addColumn(
        'firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'First Name'
    )
    ->addColumn(
        'lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Last Name'
    )
    ->addColumn(
        'email', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(), 'Email'
    )
    ->addColumn(
        'phone', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(), 'Phone'
    )
    ->addColumn(
        'school', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'School or School District'
    )
    ->addColumn(
        'position', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Position'
    )
    ->addColumn(
        'interest', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Product Interest'
    );
if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();