<?php
$installer = $this;
$installer->startSetup();
$installer->run('ALTER TABLE `'.$installer->getTable('infomodus_caship').'` ADD UNIQUE(`title`);');
$installer->endSetup();