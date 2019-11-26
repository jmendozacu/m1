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
    'Gorilla_AuthorizenetCim',
    'Gorilla_AuthorizenetCim_Model_Sales_Order_Payment',
    'Mage_Sales_Model_Order_Payment'
)) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Sales_Order_Payment_AbstractForGorilla.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Sales/Order/Payment/AbstractForGorilla.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Sales_Order_Payment_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Sales/Order/Payment/AbstractForMage.php';
    }
}