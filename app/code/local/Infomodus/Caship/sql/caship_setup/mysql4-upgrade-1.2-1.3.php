<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'tit_show_format',
    'varchar(20) NULL DEFAULT "days"'
);
$installer->endSetup();