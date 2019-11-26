<?php
/**
 * AdvancedInvoiceLayout invoice controller class
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

class Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_InvoiceController extends Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_AbstractController
{
    /**
     * @var string
     */
    protected $_documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE;

    /**
     * @var \Vianetz_AdvancedInvoiceLayout_Model_Pdf_Signature
     */
    protected $_pdfSignature;

    protected function _construct()
    {
        parent::_construct();

        $this->_pdfSignature = Mage::getModel('advancedinvoicelayout/pdf_signature');
    }

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
            $this->_forward('adminhtml/order_invoice/print', array('_current' => true));
        }
    }

    /**
     * Print PDF invoice.
     *
     * @return void
     */
    public function printpdfAction()
    {
        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if (empty($invoiceId) === true) {
            $this->_forward('noRoute');
        }

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if (empty($invoice) === true) {
            $this->_forward('noRoute');
        }

        $unsignedFilename = $this->__getFilename($invoice);
        if ($this->_pdfSignature->isSignedFileExistant($invoice) === true) {
            $this->_prepareDownloadResponse(
                basename($unsignedFilename),
                $this->_pdfSignature->get($invoice),
                'application/pdf'
            );

            return;
        }

        $this->_pdf->addSource($invoice);

        $this->_saveAndDownloadDocument($invoice);
    }

    /**
     * Overwrites original pdfinvoices action to support the configured file name in AIL settings.
     *
     * @return void
     */
    public function pdfinvoicesAction()
    {
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (empty($invoicesIds) === true) {
            $this->_redirect('*/*/');
        }

        /** @var $invoices Mage_Sales_Model_Mysql4_Order_Invoice_Collection */
        $invoices = Mage::getResourceModel('sales/order_invoice_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
            ->load();
        foreach ($invoices as $invoice) {
            $this->_pdf->addSource($invoice);
        }

        $this->_saveAndDownloadDocument($invoices);
    }


    /**
     * Mass action to for the order grid.
     *
     * @see Mage_Adminhtml_Sales_OrderController::pdfinvoicesAction()
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfinvoicesbyorderAction()
    {
        return $this->_massOrderAction();
    }

    public function signpdfAction()
    {
        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if (empty($invoiceId) === true) {
            $this->_forward('noRoute');
        }

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if (empty($invoice) === true) {
            $this->_forward('noRoute');
        }

        try {
            $filename = $this->__getFilename($invoice);

            // First we have to save the pdf.
            $this->_pdf->addSource($invoice);
            $this->_pdf->saveToFile($filename);

            $this->_pdfSignature->create($filename);
            $this->_getSession()->addNotice(Mage::helper('advancedinvoicelayout')->__('The document has been successfully signed.'));
        } catch (\Exception $exception) {
            $this->_getSession()->addError(Mage::helper('advancedinvoicelayout')->__('Error while signing document') . ": " . $exception->getMessage());
        }

        $this->getResponse()->setRedirect((Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_invoice/view', array('invoice_id' => $invoiceId))));
    }

    /**
     * @param \Mage_Sales_Model_Order_Invoice $invoice
     *
     * @return string
     */
    private function __getFilename(Mage_Sales_Model_Order_Invoice $invoice)
    {
        return Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($invoice, 'invoice');
    }
}
