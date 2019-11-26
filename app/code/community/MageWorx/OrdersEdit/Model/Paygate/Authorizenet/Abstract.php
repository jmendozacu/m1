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
    'AW_Sarp',
    'AW_Sarp_Model_Payment_Method_Core_Authorizenet',
    'Mage_Paygate_Model_Authorizenet'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paygate_Authorizenet_AbstractForAW.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paygate/Authorizenet/AbstractForAW.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_Paygate_Authorizenet_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/Paygate/Authorizenet/AbstractForMage.php';
    }
}