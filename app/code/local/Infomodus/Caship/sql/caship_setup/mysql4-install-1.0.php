<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('infomodus_caship')} (
  `caship_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `company_type` varchar(50) NOT NULL default 'custom',
  `upsmethod_id` varchar(50) NOT NULL default '',
  `dhlmethod_id` varchar(50) NOT NULL default '',
  `store_id` int(11) NOT NULL default 0,
  `is_country_all` tinyint(1) NOT NULL default 0,
  `country_ids` text NOT NULL default '',
  `price` decimal(9,2) NOT NULL default 0,
  `status` tinyint(1) NOT NULL default 0,
  `showifnot` tinyint(1) NOT NULL default 0,
  `dinamic_price` tinyint(1) NOT NULL default 0,
  `amount_min` double(9,2) NOT NULL default 0,
  `amount_max` double(9,2) NOT NULL default 0,
  `weight_min` double(5,2) NOT NULL default 0,
  `weight_max` double(5,2) NOT NULL default 0,
  `qty_min` int(11) NOT NULL default 0,
  `qty_max` int(11) NOT NULL default 0,
  `zip_min` varchar(20) NOT NULL default '',
  `zip_max` varchar(20) NOT NULL default '',
  `negotiated` tinyint(1) NOT NULL default 0,
  `negotiated_amount_from` int(11) NOT NULL default 0,
  `tax` int(2) NOT NULL default 0,
  `addday` int(2) NOT NULL default 0,
  `timeintransit` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`caship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();