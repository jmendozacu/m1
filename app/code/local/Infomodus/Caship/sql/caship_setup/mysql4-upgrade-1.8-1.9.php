<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('infomodus_caship'), 'user_group_ids',
    'text'
);
$installer->endSetup();