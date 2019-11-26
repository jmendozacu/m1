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
    'Extended_Ccsave',
    'Extended_Ccsave_Model_Payment_Method_Ccsave',
    'Mage_Payment_Model_Method_Ccsave'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Payment_Method_Ccsave_AbstractForExtendedCcsave.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Payment/Method/Ccsave/AbstractForExtendedCcsave.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Payment_Method_Ccsave_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Payment/Method/Ccsave/AbstractForMage.php';
    }
}