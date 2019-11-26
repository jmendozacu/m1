<?php
/**
 * AdvancedInvoiceLayout frontend order controller class
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
require_once(Mage::getModuleDir('controllers', 'Mage_Sales') . DS . 'OrderController.php');
class Vianetz_AdvancedInvoiceLayout_OrderController extends Mage_Sales_OrderController
{
    /**
     * @var Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    protected $_pdf;

    /**
     * @var \Vianetz_AdvancedInvoiceLayout_Model_Pdf_Signature
     */
    protected $_pdfSignature;

    /**
     * Default constructor initializes pdf model.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_pdf = Mage::getModel('advancedinvoicelayout/pdf');
        $this->_pdfSignature = Mage::getModel('advancedinvoicelayout/pdf_signature');
    }

    /**
     * Print PDF invoice in frontend.
     *
     * Note:
     * This method is very similar to Adminhtml/InvoiceController::printpdfAction() but due to different scopes
     * it is not possible to merge these two.
     *
     * @return void
     */
    public function printInvoiceAction()
    {
        if (Mage::helper('advancedinvoicelayout')->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return parent::printInvoiceAction();
        }

        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if (empty($invoiceId) === true) {
            $this->_forward('noRoute');
        }

        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if (empty($invoice) === true) {
            $this->_forward('noRoute');
        }

        $this->_pdf->addSource($invoice);

        $this->_generateAndDownloadDocument($invoice, Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE);
    }

    /**
     * Print PDF shipment in frontend.
     *
     * Note:
     * This method is very similar to Adminhtml/ShipmentController::printpdfAction() but due to different scopes
     * it is not possible to merge these two.
     *
     * @return void
     */
    public function printShipmentAction()
    {
        if (Mage::helper('advancedinvoicelayout')->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return parent::printShipmentAction();
        }

        $shipmentId = (int)$this->getRequest()->getParam('shipment_id');
        if (empty($shipmentId) === true) {
            $this->_forward('noRoute');
        }

        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        if (empty($shipment) === true) {
            $this->_forward('noRoute');
        }

        $this->_pdf->addSource($shipment);

        $this->_generateAndDownloadDocument($shipment, Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT);
    }

    /**
     * Generate and download document.
     *
     * @param Mage_Sales_Model_Abstract $source
     * @param string $documentType
     *
     * @return void
     */
    protected function _generateAndDownloadDocument(Mage_Sales_Model_Abstract $source, $documentType)
    {
        $filename = Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($source, $documentType);

        if ($source instanceof Mage_Sales_Model_Order_Invoice && $this->_pdfSignature->isSignedFileExistant($source) === true) {
            $this->_prepareDownloadResponse(
                basename($filename),
                $this->_pdfSignature->get($source),
                'application/pdf'
            );

            return;
        }

        try {
            $pdfContents = $this->_pdf->getContents();
        } catch (Exception $ex) {
            Mage::helper('advancedinvoicelayout')->logError(101, $ex->getMessage(), $ex->getTraceAsString());
            Mage::getSingleton('customer/session')->addError('An error occurred while generating ' . $documentType . ' document(s). Please contact us if this problem persists.');

            $this->_redirect('sales_order_' . $documentType . '/view', array($documentType . '_id' => $source->getOrder()->getId()));

            return;
        }

        $this->_prepareDownloadResponse(basename($filename), $pdfContents, 'application/pdf');
    }
}
