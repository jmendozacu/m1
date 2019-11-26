<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'description',
    'text'
);
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'sort',
    'int(11) NULL DEFAULT 1'
);
$installer->endSetup();