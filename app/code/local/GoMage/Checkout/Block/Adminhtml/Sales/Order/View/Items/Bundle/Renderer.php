<?php
/**
 * GoMage LightCheckout Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2018 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 5.9.4
 * @since        Class available since Release 5.3
 */

class GoMage_Checkout_Block_Adminhtml_Sales_Order_View_Items_Bundle_Renderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer
{

    function getOrderOptions($item = null)
    {
        $result = parent::getOrderOptions($item);

        if (is_null($item)) {
            $item = $this->getItem();
        }

        if ($item && $item->getData('gomage_gift_wrap')) {
            $title = Mage::helper('gomage_checkout/giftwrap')->getTitle();
            $result[] = array("value" => $this->__("Yes"), "label" => $title);
        }
        return $result;
    }

}
