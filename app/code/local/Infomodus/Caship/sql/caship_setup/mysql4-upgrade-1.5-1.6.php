<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'upsmethod_id_2',
    'varchar(50) NULL DEFAULT ""'
);
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'upsmethod_id_3',
    'varchar(50) NULL DEFAULT ""'
);
$installer->endSetup();