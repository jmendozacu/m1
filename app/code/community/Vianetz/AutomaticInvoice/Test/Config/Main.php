<?php
/**
 * VIANETZ
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
class Vianetz_AutomaticInvoice_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testCodePoolCommunity()
    {
        $this->assertModuleCodePool('community');
    }

    public function testModuleVersion()
    {
        $this->assertModuleVersion('1.1.2');
    }

    public function testModuleDependencies()
    {
        $this->assertModuleDepends('Mage_Core');
        $this->assertModuleDepends('Mage_Sales');
        $this->assertModuleDepends('Vianetz_Core');
    }

    public function testHelperAliases()
    {
        $this->assertHelperAlias('automaticinvoice', 'Vianetz_AutomaticInvoice_Helper_Data');
    }

    public function testObserverDefinedForCheckingOrderStatusChange()
    {
        $this->assertEventObserverDefined('global', 'sales_order_load_after', 'automaticinvoice/order_observer', 'registerOrderStatus', 'vianetz_automaticinvoice_order_register_status');
        $this->assertEventObserverDefined('global', 'sales_order_save_after', 'automaticinvoice/order_observer', 'dispatchOrderStatusChangeEvent', 'vianetz_automaticinvoice_order_status_change_dispatch_event');
    }

    public function testObserverDefinedForGeneratingInvoices()
    {
        $this->assertEventObserverDefined('global', 'automaticinvoice_sales_order_status_change_after', 'automaticinvoice/order_observer', 'generateInvoice', 'vianetz_automaticinvoice_order_observer_invoice');
    }

    public function testObserverDefinedForGeneratingShipments()
    {
        $this->assertEventObserverDefined('global', 'automaticinvoice_sales_order_status_change_after', 'automaticinvoice/order_observer', 'generateShipment', 'vianetz_automaticinvoice_order_observer_shipment');
    }

    public function testObserverDefinedForSendingInvoiceEmailToCustomer()
    {
        $this->assertEventObserverDefined('global', 'sales_order_invoice_pay', 'automaticinvoice/order_observer', 'sendInvoiceEmail', 'vianetz_automaticinvoice_order_observer_invoice_send_email');
    }
}