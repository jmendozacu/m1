<?php
/**
 * AdvancedInvoiceLayout Signature Model
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

require_once('Vianetz' . DS . 'Signaturportal' . DS . 'src' . DS . 'Client.php');
require_once('Vianetz' . DS . 'Signaturportal' . DS . 'src' . DS . 'SignException.php');

class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Signature
{
    /**
     * @var \Vianetz_Signaturportal_Client
     */
    private $__client;

    public function __construct()
    {
        $password = Mage::helper('core')->decrypt(
            Mage::getStoreConfig('advancedinvoicelayout/invoice/sign_password')
        );

        $this->__client = new Vianetz_Signaturportal_Client(
            Mage::getStoreConfig('advancedinvoicelayout/invoice/sign_username'),
            $password,
            Mage::getStoreConfig('advancedinvoicelayout/invoice/sign_accountno'),
            Mage::getStoreConfig('general/country/default'),
            substr(Mage::getStoreConfig('general/locale/code'), -2)
        );
    }

    /**
     * Create the PDF signature and save file to file system for later usage.
     *
     * @param string $filename
     *
     * @return string the signed pdf contents
     * @throws \SOAPFault
     */
    public function create($filename)
    {
        $signedContents = $this->__client->sign($filename);

        @file_put_contents($this->getSignedFilename($filename), $signedContents);

        return $signedContents;
    }

    /**
     * @param string $unsignedFilename
     *
     * @return string
     */
    public function getSignedFilename($unsignedFilename)
    {
        return str_replace('.pdf', '_signed.pdf', $unsignedFilename);
    }

    /**
     * @param \Mage_Sales_Model_Order_Invoice $invoice
     *
     * @return bool
     */
    public function isSignedFileExistant(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $unsignedFilename = Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($invoice, 'invoice');

        return file_exists($this->getSignedFilename($unsignedFilename));
    }

    /**
     * @param \Mage_Sales_Model_Order_Invoice $invoice
     *
     * @return string
     * @throws \Vianetz_AdvancedInvoiceLayout_Exception
     */
    public function get(Mage_Sales_Model_Order_Invoice $invoice)
    {
        if ($this->isSignedFileExistant($invoice) === false) {
            throw new Vianetz_AdvancedInvoiceLayout_Exception('Signed file does not exist, you have to create a PDF signature first.');
        }

        $unsignedFilename = Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($invoice, 'invoice');

        return file_get_contents($this->getSignedFilename($unsignedFilename));
    }
}