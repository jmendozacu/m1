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
    'CLS_Paypal_Model_Paypal_Api_Nvp',
    'Mage_Paypal_Model_Api_Nvp'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Api_Nvp_AbstractForCLS.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Api/Nvp/AbstractForCLS.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paypal_Api_Nvp_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paypal/Api/Nvp/AbstractForMage.php';
    }
}