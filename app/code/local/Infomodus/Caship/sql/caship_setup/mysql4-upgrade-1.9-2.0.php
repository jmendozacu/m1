<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'product_categories',
    'text'
);
$installer->endSetup();