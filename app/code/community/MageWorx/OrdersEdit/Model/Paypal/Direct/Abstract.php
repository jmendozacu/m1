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
    'CLS_Paypal_Model_Paypal_Direct',
    'Mage_Paypal_Model_Direct'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Direct_AbstractForCLS.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Direct/AbstractForCLS.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Direct_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Direct/AbstractForMage.php';
    }
}