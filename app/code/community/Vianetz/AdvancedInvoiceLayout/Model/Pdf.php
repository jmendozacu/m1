<?php
/**
 * AdvancedInvoiceLayout Public Pdf Model
 *
 * This class wraps all the internal processes and is the main class that is intended to be used by developers.
 * Usage:
 * 1) Instantiate (optionally with your custom generator class)
 * 2) addSource($invoice)
 * 3) getContents() or saveToFile()
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
final class Vianetz_AdvancedInvoiceLayout_Model_Pdf extends Vianetz_Pdf_Model_Pdf
{
    /**
     * @var null|Mage_Core_Model_Store
     */
    private $store = null;

    /**
     * Add a new document to generate.
     *
     * @param Mage_Sales_Model_Abstract $source
     *
     * @api
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    public function addSource(Mage_Sales_Model_Abstract $source)
    {
        $this->setStore($source->getStore());

        $documentType = Mage::getModel('advancedinvoicelayout/source_type')->getTypeBySource($source);
        /** @var Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Abstract $document */
        $document = Mage::getModel('advancedinvoicelayout/pdf_document_' . $documentType)
            ->setSource($source);

        $document->setIsPrintedInAdmin($this->isInAdminArea());

        $this->addDocument($document);

        return $this;
    }

    /**
     * Initializes configured options.
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    protected function init()
    {
        $this->initConfiguration();

        return $this;
    }

    /**
     * Checks whether the current scope is in admin or not.
     *
     * @return boolean
     */
    private function isInAdminArea()
    {
        return Mage::app()->getStore()->isAdmin();
    }

    /**
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    private function initConfiguration()
    {
        $this->config->isDebugMode = Mage::getStoreConfigFlag('advancedinvoicelayout/general/enable_debug_mode', $this->store);
        $this->config->pdfSize = Mage::getStoreConfig('advancedinvoicelayout/pdf_options/pdf_size', $this->store);
        $this->config->pdfOrientation = Mage::getStoreConfig('advancedinvoicelayout/pdf_options/pdf_orientation', $this->store);
        $this->config->pdfAuthor = Mage::getStoreConfig('advancedinvoicelayout/pdf_options/author', $this->store);
        $this->config->tempDir = Mage::getBaseDir('tmp');

        return $this;
    }

    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    private function setStore(Mage_Core_Model_Store $store)
    {
        $this->store = $store;

        $this->initConfiguration();

        return $this;
    }
}
