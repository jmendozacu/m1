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
    'MageTools_Pendingorders',
    'MageTools_Pendingorders_Block_Sales_Order_View',
    'Mage_Adminhtml_Block_Sales_Order_View'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForMageTools.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForMageTools.php';
    }
}
elseif(MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'IllApps_Shipsync',
    'IllApps_Shipsync_Block_Adminhtml_Sales_Order_View',
    'Mage_Adminhtml_Block_Sales_Order_View'
)) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForIllApps.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForIllApps.php';
    }
}
elseif(MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'AuIt_Pdf',
    'AuIt_Pdf_Block_Adminhtml_Sales_Order_View',
    'Mage_Adminhtml_Block_Sales_Order_View'
)) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForAuIt.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForAuIt.php';
    }
}
elseif(MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'Amasty_Email',
    'Amasty_Email_Block_Adminhtml_Sales_Order_View',
    'Mage_Adminhtml_Block_Sales_Order_View'
)) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForAmasty.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForAmasty.php';
    }
}
elseif(MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'Viabill_Payepay',
    'Viabill_Payepay_Block_Adminhtml_Sales_Order_View',
    'Mage_Adminhtml_Block_Sales_Order_View'
)) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForViabill.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForViabill.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Block/Adminhtml/Sales/Order/View/AbstractForMage.php';
    }
}