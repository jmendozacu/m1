<?php
/**
 * AdvancedInvoiceLayout Email Observer
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
class Vianetz_AdvancedInvoiceLayout_Model_Email_Observer
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
    public function __construct()
    {
        $this->_pdf = Mage::getModel('advancedinvoicelayout/pdf');
        $this->_pdfSignature = Mage::getModel('advancedinvoicelayout/pdf_signature');
    }

    /**
     * Attach the pdf document to the email.
     *
     * Event: vianetz_pdfattachments_email_send_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Email_Observer
     */
    public function addPdfAttachment(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = $observer->getEvent()->getEmailTemplate();
        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = $observer->getEvent()->getMailer();
        /** @var Mage_Core_Model_Email_Info $emailInfo */
        $emailInfo = $observer->getEvent()->getEmailInfo();

        $documentType = $this->_getDocumentTypeByTemplate($mailer);
        $isAttachmentEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/' . $documentType . '/attach_to_email', $mailer->getStoreId());
        if ($documentType === null || $isAttachmentEnabled !== true) {
            Mage::helper('advancedinvoicelayout')->log('Skipping attachment for document type ' . $documentType . ' for template ' . $mailer->getTemplateId() . ' / ' . $emailTemplate->getTemplateSubject() . '..');
            return $this;
        }

        try {
            $contents = $this->_getAttachmentContents($mailer);
            if (empty($contents) === true) {
                throw new Vianetz_AdvancedInvoiceLayout_Exception('No ' . $documentType . ' PDF contents to attach to email.');
            }
            Mage::helper('pdfattachments')->addAttachmentToEmail($emailTemplate, $contents, $this->_getAttachmentFilename($mailer));
            Mage::helper('advancedinvoicelayout')->log('Attached ' . $documentType . ' PDF to email ' . join(',', $emailInfo->getToEmails()) . '..');
        } catch (Exception $ex) {
            Mage::helper('advancedinvoicelayout')->logError(105, $ex->getMessage(), $ex->getTraceAsString());
        }

        return $this;
    }
    
    /**
     * Get source model based on document type.
     *
     * @param Mage_Core_Model_Email_Template_Mailer $mailer
     *
     * @return null|Mage_Sales_Model_Abstract
     */
    protected function _getSourceModel(Mage_Core_Model_Email_Template_Mailer $mailer)
    {
        $templateParams = $mailer->getTemplateParams();
        $documentType = $this->_getDocumentTypeByTemplate($mailer);

        if (isset($templateParams[$documentType]) !== true) {
            return null;
        }

        return $templateParams[$documentType];
    }

    /**
     * Get document type based on email template id.
     *
     * @param Mage_Core_Model_Email_Template_Mailer $mailer
     *
     * @return null|string
     */
    protected function _getDocumentTypeByTemplate(Mage_Core_Model_Email_Template_Mailer $mailer)
    {
        $documentType = null;

        switch ($mailer->getTemplateId()) {
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE, $mailer->getStoreId()):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_GUEST_TEMPLATE, $mailer->getStoreId()):
                $documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE;
                break;
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_TEMPLATE, $mailer->getStoreId()):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_GUEST_TEMPLATE, $mailer->getStoreId()):
                $documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT;
                break;
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_TEMPLATE, $mailer->getStoreId()):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_GUEST_TEMPLATE, $mailer->getStoreId()):
                $documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO;
                break;
        }

        return $documentType;
    }

    /**
     * Get filename for email attachment.
     *
     * @param Mage_Core_Model_Email_Template_Mailer $mailer
     *
     * @return string
     */
    protected function _getAttachmentFilename(Mage_Core_Model_Email_Template_Mailer $mailer)
    {
        $documentType = $this->_getDocumentTypeByTemplate($mailer);

        return basename(Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($this->_getSourceModel($mailer), $documentType));
    }

    /**
     * Get email attachment contents based on source model.
     *
     * @param Mage_Core_Model_Email_Template_Mailer $mailer
     *
     * @return string
     */
    protected function _getAttachmentContents(Mage_Core_Model_Email_Template_Mailer $mailer)
    {
        $sourceModel = $this->_getSourceModel($mailer);
        if (empty($sourceModel) === true) {
            return '';
        }

        $this->_pdf->addSource($sourceModel);

        if ($sourceModel instanceof Mage_Sales_Model_Order_Invoice && $this->_isPdfSigningEnabled($sourceModel->getStore()) === true) {
            if ($this->_pdfSignature->isSignedFileExistant($sourceModel) === true) {
                return $this->_pdfSignature->get($sourceModel);
            }

            $unsignedFilename = Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($sourceModel, 'invoice');

            $this->_pdf->saveToFile($unsignedFilename);
            $signedPdfContents = $this->_pdfSignature->create($unsignedFilename);

            return $signedPdfContents;
        }

        return $this->_pdf->getContents();
    }

    /**
     * @param \Mage_Core_Model_Store $store
     *
     * @return bool
     */
    protected function _isPdfSigningEnabled(Mage_Core_Model_Store $store)
    {
        return Mage::getStoreConfigFlag('advancedinvoicelayout/invoice/sign_email_attachments', $store);
    }
}
