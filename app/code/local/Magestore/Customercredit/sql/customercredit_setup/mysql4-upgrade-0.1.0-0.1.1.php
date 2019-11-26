<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
//        $product2 = new Mage_Catalog_Model_Product();
//        $product2->setStoreId(0)
//                ->setId(null)
//                ->setAttributeSetId(9)
//                ->setTypeId("customercredit")
//                ->setName("Credit product 2")
//                ->setSku("creditproduct")
//                ->setStatus("1")
//                ->setTaxClassId("0")
//                ->setVisibility("4")
//                ->setEnableGooglecheckout("1")
//                ->setCreditAmount("1-10000")
//                ->setDescription("Credit product")
//                ->setShortDescription("credit")
//                ->save();
//    $catalogproduct = Mage::getModel('catalog/product')->getCollection()
//                                                        ->setOrder('entity_id', 'DESC')
//                                                        ->getFirstItem();
//    $maxProductId = $catalogproduct->getId();
//    $catalog_product_entity = 'insert  into `catalog_product_entity`(`entity_id`,`entity_type_id`,`attribute_set_id`,`type_id`,`sku`,`created_at`,`updated_at`,`has_options`,`required_options`) values ';	
//    $catalog_product_entity .= "(".$entity_id.",10,38,'simple','".$sku."','2007-08-23 13:03:05','2008-08-08 14:50:04',0,0)";
//    $catalog_product_entity_decimal = 'insert  into `catalog_product_entity_decimal`(`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values ';
//    $catalog_product_entity_int = 'insert  into `catalog_product_entity_int`(`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`)	values ';
//    $catalog_product_entity_text = 'insert  into `catalog_product_entity_text`(`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values ';
//    $catalog_product_entity_varchar = 'insert  into `catalog_product_entity_varchar`(`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values ';
//    $cataloginventory_stock_item = 'insert  into `cataloginventory_stock_item`(`product_id`,`stock_id`,`qty`,`min_qty`,`use_config_min_qty`,`is_qty_decimal`,`backorders`,`use_config_backorders`,`min_sale_qty`,`use_config_min_sale_qty`,`max_sale_qty`,`use_config_max_sale_qty`,`is_in_stock`,`low_stock_date`,`notify_stock_qty`,`use_config_notify_stock_qty`,`manage_stock`,`use_config_manage_stock`,`stock_status_changed_auto`,`use_config_qty_increments`,`qty_increments`,`use_config_enable_qty_inc`,`enable_qty_increments`) values ';
//    
//   
//    $catalog_product_entity_decimal .= "(10,101,0,".$entity_id.",'3.2000'),(10,99,0,".$entity_id.",'149.9900'),(10,100,0,".$entity_id.",'20.0000'),(10,503,0,".$entity_id.",'149.9900')";
//    $catalog_product_entity_int .= "(10,273,0,".$entity_id.",1),(10,274,0,".$entity_id.",2),(10,272,0,".$entity_id.",24),(10,526,0,".$entity_id.",4)";
//    $catalog_product_entity_text .= "(10,495,0,".$entity_id.",'2610'),(10,494,0,".$entity_id.",'4.1 x 1.7 x 0.7 inches '),(10,97,0,".$entity_id.",'The Nokia 2610 is '),(10,492,0,".$entity_id.",'Integrated camera'),(10,496,0,".$entity_id.",'Conditional $2'),(10,104,0,".$entity_id.",'Nokia 2610, cell, phone, '),(10,506,0,".$entity_id.",'the bank'),(10,531,0,".$entity_id.",'')";
//    $name = "Michael Magestore ".$entity_id;
//    $catalog_product_entity_varchar .= "(10,96,0,".$entity_id.",'".$name."'),(10,102,0,".$entity_id.",'20'),(10,481,0,".$entity_id.",'nokia-2610-phone'),(10,103,0,".$entity_id.",'Nokia 2610'),(10,105,0,".$entity_id.",'The Nokia 2610 is an easy to use'),(10,106,0,".$entity_id.",'/n/o/nokia-2610-phone-2.jpg'),(10,109,0,".$entity_id.",'/n/o/nokia-2610-phone-2.jpg'),(10,493,0,".$entity_id.",'/n/o/nokia-2610-phone-2.jpg'),(10,562,0,".$entity_id.",''),(10,836,0,".$entity_id.",'container2'),(10,571,0,".$entity_id.",''),(10,570,1,".$entity_id.",'nokia-2610-phone.html'),(10,570,0,".$entity_id.",'nokia-2610-phone.html')";
//    $qty = rand().'.0000';
//    $cataloginventory_stock_item .= "(".$entity_id.",1,'".$qty."','0.0000',1,0,0,1,'1.0000',1,'100.0000',1,1,'0000-00-00 00:00:00',NULL,1,0,1,0,1,'0.0000',1,0)";
//			
//    
//    
    
    
    $installer->endSetup();