<?php
/**
 * AdvancedInvoiceLayout Pdf abstract class
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
/**
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Abstract
 */
abstract class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Abstract extends Vianetz_Pdf_Model_Document_Abstract implements Vianetz_Pdf_Model_Document_Interface
{
    /**
     * @var Mage_Sales_Model_Abstract
     */
    protected $source;

    /**
     * @var boolean
     */
    private $isPrintedInAdmin = false;

    /**
     * @return boolean
     */
    public function getIsPrintedInAdmin()
    {
        return $this->isPrintedInAdmin;
    }

    /**
     * @param bool $isPrintedInAdmin
     *
     * @return Vianetz_Pdf_Model_Document_Abstract
     */
    public function setIsPrintedInAdmin($isPrintedInAdmin)
    {
        $this->isPrintedInAdmin = $isPrintedInAdmin;
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Abstract $source
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Abstract
     */
    public function setSource(Mage_Sales_Model_Abstract $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Abstract
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Return HTML file contents for one single invoice/shipment/creditmemo that will be converted into PDF.
     * We do not use layout xml as otherwise PDF cannot be generated from cron easily.
     *
     * @return string
     */
    public function getHtmlContents()
    {
        // Registering invoice is necessary because otherwise the tax values are not calculated correctly.
        // @see Mage_Tax_Helper_Data::getCalculatedTaxes()
        Mage::unregister('current_' . $this->getDocumentType());
        Mage::register('current_' . $this->getDocumentType(), $this->getSource());

        $blockAttributes = array(
            'source' => $this->getSource(),
            'is_printed_in_admin' => $this->getIsPrintedInAdmin()
        );

        $footerBlock = Mage::getSingleton('core/layout')
            ->createBlock('advancedinvoicelayout/pdf_footer', 'advancedinvoicelayout_pdf_' . $this->getDocumentType() . '_footer', $blockAttributes);

        $headerBlock = Mage::getSingleton('core/layout')
            ->createBlock('advancedinvoicelayout/pdf_header', 'advancedinvoicelayout_pdf_' . $this->getDocumentType() . '_header', $blockAttributes);

        $totalsBlock = Mage::getSingleton('core/layout')
            ->createBlock('advancedinvoicelayout/pdf_totals', 'advancedinvoicelayout_pdf_' . $this->getDocumentType() . '_totals', $blockAttributes);

        return Mage::getSingleton('core/layout')
            ->createBlock('advancedinvoicelayout/pdf_' . $this->getDocumentType(), 'advancedinvoicelayout_pdf_' . $this->getDocumentType(), $blockAttributes)
            ->setChild('header', $headerBlock)
            ->setChild('footer', $footerBlock)
            ->setChild('totals', $totalsBlock)
            ->toHtml();
    }

    /**
     * Return store id for current document (will be used e.g. for store emulation).
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->getSource()->getStore()->getId();
    }

    /**
     * @return string
     */
    public function getPdfBackgroundFile()
    {
        return $this->getPdfFileFromConfig('pdf_template_file');
    }

    /**
     * @return string
     */
    public function getPdfBackgroundFileForFirstPage()
    {
        return $this->getPdfFileFromConfig('pdf_template_file_first_page');
    }

    /**
     * @return string
     */
    public function getPdfAttachmentFile()
    {
        return $this->getPdfFileFromConfig('pdf_attachment_file');
    }

    /**
     * Return configured PDF file.
     *
     * @param string $configPath
     *
     * @return string
     */
    protected function getPdfFileFromConfig($configPath)
    {
        $frontendTemplateFile = Mage::getStoreConfig('advancedinvoicelayout/general/' . $configPath, $this->getStoreId());
        if (empty($frontendTemplateFile) === false) {
            $frontendTemplateFile = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'invoices' . DS . 'template' . DS . $frontendTemplateFile;
        }

        return $frontendTemplateFile;
    }
}
