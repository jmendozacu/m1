<?php
/**
 * AdvancedInvoiceLayout general helper class
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
class Vianetz_AdvancedInvoiceLayout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML Admin Path For Signature Username
     *
     * @var string
     */
    const XML_PATH_SALES_PDF_INVOICE_SIGN_USERNAME = 'advancedinvoicelayout/invoice/sign_username';

    /**
     * XML Admin Path For Signature Password
     * @var string
     */
    const XML_PATH_SALES_PDF_INVOICE_SIGN_PASSWORD = 'advancedinvoicelayout/invoice/sign_password';

    /**
     * XML Admin Path For Signature Account Number
     * @var string
     */
    const XML_PATH_SALES_PDF_INVOICE_SIGN_ACCOUNTNO = 'advancedinvoicelayout/invoice/sign_accountno';

    /**
     * @var string
     */
    const XML_PATH_GENERAL_COUNTRY_DEFAULT = 'general/country/default';

    /**
     * @var string
     */
    const XML_PATH_GENERAL_LOCALE_CODE = 'general/locale/code';

    /**
     * Log a message to file.
     *
     * @param string $message
     * @param int $type
     *
     * @return Vianetz_AdvancedInvoiceLayout_Helper_Data
     */
    public function log($message, $type = LOG_DEBUG)
    {
        Mage::helper('vianetz_core/log')->log($message, $type, 'Vianetz_AdvancedInvoiceLayout');
        return $this;
    }

    /**
     * Log a error message to file.
     *
     * @param int $errorCode
     * @param string $errorMessage
     * @param string $errorTrace
     * @param int $type
     *
     * @return Vianetz_AdvancedInvoiceLayout_Helper_Data
     */
    public function logError($errorCode, $errorMessage, $errorTrace = '', $type = LOG_ERR)
    {
        $message = 'Error (' . $errorCode . '): ' . $errorMessage . "\n" . $errorTrace;
        return $this->log($message, $type);
    }

    /**
     * Check if module is activated for specified document type.
     *
     * @param string $documentType The document type to check.
     *
     * @return boolean
     */
    public function isModuleActive($documentType)
    {
        if (Mage::helper('advancedinvoicelayout')->isModuleOutputEnabled() === false) {
            return false;
        }

        if (Mage::getStoreConfigFlag('advancedinvoicelayout/' . $documentType . '/enable') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if print action is allowed for specified document type.
     *
     * @param string $documentType The document type to check.
     *
     * @return boolean
     */
    public function isPrintActionAllowed($documentType)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('advancedinvoicelayout/actions/print_' . $documentType) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param $invoiceId
     * @throws SoapFault
     */
    public function signInvoice($invoiceId)
    {
    	$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        $invoices = array($invoice);

        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);

        $oSOAP = new Vianetz_AdvancedInvoiceLayout_Model_SignClient();
        $downloadDir = Mage::getBaseDir('media') . '/invoices/';
        $filename = "INV-" . $invoice->getIncrementId() . ".pdf";

        $username = Mage::getStoreConfig(self::XML_PATH_SALES_PDF_INVOICE_SIGN_USERNAME);
        $password = Mage::getStoreConfig(self::XML_PATH_SALES_PDF_INVOICE_SIGN_PASSWORD);
        $accountno = Mage::getStoreConfig(self::XML_PATH_SALES_PDF_INVOICE_SIGN_ACCOUNTNO);
        $country = Mage::getStoreConfig(self::XML_PATH_GENERAL_COUNTRY_DEFAULT);
        $locale = substr(Mage::getStoreConfig(self::XML_PATH_GENERAL_LOCALE_CODE), -2);

        try {
        	Mage::helper('advancedinvoicelayout')->decode($downloadDir, $oSOAP->sign($username, $password, $accountno, $filename, Mage::helper('advancedinvoicelayout')->encode($downloadDir . $filename), $country, $locale, ''), Mage::helper('advancedinvoicelayout')->getTargetFilename($downloadDir . $filename));
        } catch(SOAPFault $ex) {
        	Mage::logException($ex);
        	throw $ex;
        }
        @unlink($downloadDir . $filename);
    }

    /**
     * Generate QR code image and write it to stdout.
     *
     * @static
     *
     * @param $text
     */
    public static function generateQrCode($text)
    {
        $config = Mage::helper('qrmage/config');

        $image = $config->getSwetakeImage();
        $level = $config->getLevel();

        $dataPath = Mage::getBaseDir('media');
        $dataPath .= "/qrmage";

        $helper = Mage::helper('qrmage/swetake');

        $helper->setQrCodeDataString($text)
            ->setConfigDataPath($dataPath)
            ->setQrCodeErrorCorrect($level)
            ->setQrCodeImageType($image)
            ->setQrCodeModuleSize(4);

        $helper->createQrCode();
    }
}
