<?php
/**
 * AdvancedInvoiceLayout shipment controller class
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
require_once(Mage::getModuleDir('controllers', 'Vianetz_AdvancedInvoiceLayout') . DS . 'Adminhtml' . DS . 'Advancedinvoicelayout' . DS . 'AbstractController.php');
class Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_ShipmentController extends Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_AbstractController
{
    /**
     * @var string
     */
    protected $_documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT;

    /**
     * Check if module is active (System->Configuration->Advanced) & System->Configuration->AdvancedInvoiceLayout->Invoice/Shipment/Creditmemo.
     *
     * @return Mage_Adminhtml_Controller_Action|void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (Mage::helper('advancedinvoicelayout')->isModuleActive($this->_documentType) === false) {
            // We print the default template if module is not active.
            $this->_forward('adminhtml/order_shipment/print', array('_current' => true));
        }
    }

    /**
     * Print PDF shipment.
     *
     * @return void
     */
    public function printpdfAction()
    {
        $shipmentId = (int)$this->getRequest()->getParam('shipment_id');
        if (empty($shipmentId) === true) {
            $this->_forward('noRoute');
        }

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        if (empty($shipment) === true) {
            $this->_forward('noRoute');
        }

        $this->_pdf->addSource($shipment);

        $this->_saveAndDownloadDocument($shipment);
    }

    /**
     * Overwrites original pdfshipments action to support the configured file name in AIL settings.
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfshipmentsAction()
    {
        $shipmentsIds = $this->getRequest()->getPost('shipment_ids');
        if (empty($shipmentsIds) === true) {
            $this->_redirect('*/*/');
        }

        /** @var $shipments Mage_Sales_Model_Mysql4_Order_Shipment_Collection */
        $shipments = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array( 'in' => $shipmentsIds ))
            ->load();
        foreach ($shipments as $shipment) {
            $this->_pdf->addSource($shipment);
        }

        $this->_saveAndDownloadDocument($shipments);
    }

    /**
     * Overwrites original pdfshipments action to support the configured file name in AIL settings.
     *
     * @see Mage_Adminhtml_Sales_OrderController::pdfshipmentsAction()
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfshipmentsbyorderAction()
    {
        return $this->_massOrderAction();
    }
}
