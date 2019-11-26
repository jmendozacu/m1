<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'CLS_Paypal',
    'CLS_Paypal_Model_Paypal_Payflowpro',
    'Mage_Paypal_Model_Payflowpro'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Payflowpro_AbstractForCLS.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Payflowpro/AbstractForCLS.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Payflowpro_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Payflowpro/AbstractForMage.php';
    }
}