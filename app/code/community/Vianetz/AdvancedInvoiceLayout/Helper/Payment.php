<?php
/**
 * AdvancedInvoiceLayout helper class for payment methods
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz\AdvancedInvoiceLayout
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
* @copyright   Copyright (c) 2006-18 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */
class Vianetz_AdvancedInvoiceLayout_Helper_Payment extends Mage_Core_Helper_Abstract
{
    /**
     * Payment method code for Billsafe payments.
     */
    const PAYMENT_METHOD_CODE_BILLSAFE = 'billsafe';

    /**
     * Return the code of the payment method for given order.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    public function isBillSafePayment(Mage_Sales_Model_Order $order)
    {
        return $order->getPayment()->getMethodInstance()->getCode() === self::PAYMENT_METHOD_CODE_BILLSAFE;
    }
}
