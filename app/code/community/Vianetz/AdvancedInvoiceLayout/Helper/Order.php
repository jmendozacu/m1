<?php
/**
 * AdvancedInvoiceLayout order helper class
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
class Vianetz_AdvancedInvoiceLayout_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return null|string
     */
    public function getShippingDate(Mage_Sales_Model_Order $order)
    {
        $shipmentCollection = $order->getShipmentsCollection();
        if ($shipmentCollection === false) {
            return null;
        }

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $shipmentCollection->getFirstItem();

        try {
            $shippingDate = $shipment->getCreatedAtDate();
        } catch (Exception $exception) {
            // There maybe some "Date part not found" errors in Zend_Date thats why we catch it here.
            $shippingDate = null;
        }

        return $shippingDate;
    }
}
