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
 * @since        Class available since Release 2.4
 */
class GoMage_Checkout_Block_Checkout_Giftwrap extends Mage_Checkout_Block_Total_Default
{

    protected $_template = 'gomage/checkout/giftwrap/totals.phtml';

    function displayBoth()
    {
        return Mage::helper('gomage_checkout/giftwrap')->displayBoth();
    }

    function getValues()
    {
        $values = array();
        $total  = $this->getTotal();

        $totals = Mage::helper('gomage_checkout/giftwrap')->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }

        return $values;
    }

}