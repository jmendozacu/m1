<?php
/**
 * AutomaticInvoice Save Shipment Workflow Class
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
class Vianetz_AutomaticInvoice_Model_Workflow_Order_Save_Shipment extends Vianetz_AutomaticInvoice_Model_Workflow_Order_Save_Abstract
{
    /**
     * @return string
     */
    public function getSourceType()
    {
        return 'shipment';
    }
}