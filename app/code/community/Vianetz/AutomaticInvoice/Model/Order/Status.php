<?php
/**
 * AutomaticInvoice Order Status Model
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
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
class Vianetz_AutomaticInvoice_Model_Order_Status extends Mage_Core_Model_Abstract
{
    /**
     * Registry key for saving order status changes.
     *
     * @var string
     */
    const REGISTRY_KEY_ORDER_STATUS = 'automaticinvoice_order_status';

    /**
     * Save order status to registry.
     *
     * @param string $newOrderStatus
     * @param boolean $forceWrite Force status writing.
     *
     * @return string
     */
    public function saveToRegistry($newOrderStatus, $forceWrite = false)
    {
        if ($forceWrite === true) {
            Mage::unregister(self::REGISTRY_KEY_ORDER_STATUS);
        }

        $orderStatus = Mage::registry(self::REGISTRY_KEY_ORDER_STATUS);
        if (empty($orderStatus) === true) {
            Mage::register(self::REGISTRY_KEY_ORDER_STATUS, $newOrderStatus);
        }

        return $orderStatus;
    }

    /**
     * Return order status from registry.
     *
     * @return string
     */
    public function getFromRegistry()
    {
        return Mage::registry(self::REGISTRY_KEY_ORDER_STATUS);
    }
}