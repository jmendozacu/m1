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
class Vianetz_AutomaticInvoice_Test_Model_Order_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @loadFixture invoiceWithNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailSentIfInvoiceIsPaid()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->once())->method('sendEmail')->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_PAID));
        $invoiceMock->expects($this->once())->method('getEmailSent')->will($this->returnValue(false));
        $this->replaceByMock('model', 'sales/order_invoice', $invoiceMock);

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }

    /**
     * @loadFixture invoiceWithoutNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailNotSentIfConfigDisabled()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->once())->method('sendEmail')->with($this->equalTo(false))->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_PAID));
        $this->replaceByMock('model', 'sales/order_invoice', $invoiceMock);

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }

    /**
     * @loadFixture invoiceWithoutNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailNotSentIfInvoiceStateOpen()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->never())->method('sendEmail')->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_OPEN));
        $invoiceMock->expects($this->never())->method('getEmailSent')->will($this->returnValue(false));
        $this->replaceByMock('model', 'sales/order_invoice', $invoiceMock);

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }
}