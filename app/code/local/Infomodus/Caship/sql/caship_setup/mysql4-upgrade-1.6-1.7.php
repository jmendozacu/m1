<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'direction_type',
    'int(11) NULL DEFAULT 1'
);
$installer->endSetup();