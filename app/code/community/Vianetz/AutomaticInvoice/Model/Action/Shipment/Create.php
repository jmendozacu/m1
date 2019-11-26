<?php
/**
 * AutomaticInvoice Shipment Create Action Class
 *
 * NOTICE
 * Magento 1.4.x has a bug with the prepareShipment() function in app/code/core/Mage/Sales/Model/Order.php
 * (Please apply the patch mentioned in http://magebase.com/magento-tutorials/shipment-api-in-magento-1-4-1-broken/)s
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
class Vianetz_AutomaticInvoice_Model_Action_Shipment_Create extends Vianetz_AutomaticInvoice_Model_Action_Abstract
{
    /**
     * Check whether the action is allowed to be executed or not.
     *
     * @return boolean
     */
    public function canRun()
    {
        if ($this->order->canShip() === false) {
            Mage::helper('automaticinvoice')->log('Order #' . $this->order->getId() . ' cannot be shipped.');
            return false;
        }

        return true;
    }

    /**
     * Generate shipment automatically based on several config values.
     *
     * @return Vianetz_AutomaticInvoice_Model_Action_Shipment_Create
     */
    public function run()
    {
        if ($this->canRun() === false) {
            return $this;
        }

        $store = $this->order->getStore();
        $shipment = $this->order->prepareShipment();

        $shipment->register();
        $this->order->setIsInProcess(true);

        Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        $this->order->addRelatedObject($shipment);

        // Set order status.
        $this->order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING,
            Mage::getStoreConfig('automaticinvoice/shipment/new_order_status', $store),
            Mage::helper('automaticinvoice')->__('Shipment #' . $shipment->getId() . ' automatically generated.')
        );
        $this->order->save();

        Mage::helper('automaticinvoice')->log('Shipment #' . $shipment->getId() . ' has been generated successfully.', LOG_INFO);

        $this->result = $shipment;

        return $this;
    }
}