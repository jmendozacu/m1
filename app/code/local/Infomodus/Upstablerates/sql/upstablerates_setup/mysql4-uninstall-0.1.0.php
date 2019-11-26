<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('shipping_upstablerates')};
DELETE FROM {$this->getTable('core/config_data')} WHERE path like 'carriers/upstablerates/%';
");

$installer->endSetup();
