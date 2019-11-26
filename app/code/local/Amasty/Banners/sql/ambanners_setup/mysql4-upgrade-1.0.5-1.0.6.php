<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
 	ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `show_on_search` text DEFAULT NULL
");
$this->endSetup();
