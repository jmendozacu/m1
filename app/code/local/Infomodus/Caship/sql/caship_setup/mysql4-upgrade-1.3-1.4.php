<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'added_value_type',
    'varchar(20) NULL DEFAULT "static"'
);
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'added_value',
    'double(7,2) NULL DEFAULT 0'
);
$installer->endSetup();