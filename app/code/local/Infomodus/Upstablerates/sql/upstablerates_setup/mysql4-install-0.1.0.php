<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('shipping_upstablerates')};
CREATE TABLE {$this->getTable('shipping_upstablerates')} (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `website_id` int(11) NOT NULL default '0',
  `dest_country_id` varchar(4) NOT NULL default '0',
  `dest_region_id` int(10) NOT NULL default '0',
  `dest_zip` varchar(10) NOT NULL default '',
  `condition_name` varchar(20) NOT NULL default '',
  `condition_value` decimal(12,4) NOT NULL default '0.0000',
  `condition_type` varchar(16) NOT NULL default 'value',
  `method_code` varchar(64) NOT NULL,
  `method_name` varchar(64) NOT NULL,
  `method_description` varchar(255) NOT NULL,
  `price` decimal(12,4) NOT NULL default '0.0000',
  `cost` decimal(12,4) NOT NULL default '0.0000',
  `way` int(11) NOT NULL default '2',
  PRIMARY KEY  (`pk`)
) DEFAULT CHARSET=utf8;
   ");

$installer->endSetup();
