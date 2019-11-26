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
    'Amasty_Rules',
    'Amasty_Rules_Model_SalesRule_Quote_Discount',
    'Mage_SalesRule_Model_Quote_Discount'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_AbstractForAmastyRules.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/SalesRule/Quote/Discount/AbstractForAmastyRules.php';
    }
}
elseif (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck(
    'Amasty_Promo',
    'Amasty_Promo_Model_SalesRule_Quote_Discount',
    'Mage_SalesRule_Model_Quote_Discount'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_AbstractForAmastyPromo.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/SalesRule/Quote/Discount/AbstractForAmastyPromo.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_AbstractForMage.php';
    } else {
        require_once 'MageWorx/OrdersEdit/Model/SalesRule/Quote/Discount/AbstractForMage.php';
    }
}