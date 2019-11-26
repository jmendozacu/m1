<?php
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Migrate config settings.
$installer->updateTableRow('core/config_data', 'path', 'advancedinvoicelayout/general/sender_address', 'path', 'advancedinvoicelayout/layout_header/sender_address');
$installer->updateTableRow('core/config_data', 'path', 'advancedinvoicelayout/general/footertext_1column', 'path', 'advancedinvoicelayout/layout_footer/footertext_1column');
$installer->updateTableRow('core/config_data', 'path', 'advancedinvoicelayout/general/footertext_2column', 'path', 'advancedinvoicelayout/layout_footer/footertext_2column');
$installer->updateTableRow('core/config_data', 'path', 'advancedinvoicelayout/general/footertext_3column', 'path', 'advancedinvoicelayout/layout_footer/footertext_3column');

$installer->endSetup();
