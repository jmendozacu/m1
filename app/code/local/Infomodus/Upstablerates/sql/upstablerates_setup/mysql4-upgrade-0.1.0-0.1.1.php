<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('shipping_upstablerates'), 'sort',
    'int(11) NULL DEFAULT \'0\' AFTER `way`'
);
$installer->endSetup();