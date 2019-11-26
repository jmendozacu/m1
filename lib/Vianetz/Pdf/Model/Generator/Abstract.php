<?php
/**
 * Pdf generator abstract class
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
 * @package     Vianetz_Pdf
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */
/**
 * Class Vianetz_Pdf_Model_Abstract
 *
 * A note on PDF merging:
 * - each generator instance generates the PDF of one source
 * - the merge() method in this class takes care of the merging of the single files
 * This is necessary because the documents may contain source dependent information in header/footer like invoice id,
 * addresses, etc. that cannot be determined correctly if you put all in one file.
 *
 * A note on naming convention:
 * - "source" means the Magento instance of an invoice/shipment/creditmemo (= Mage_Sales_Model_Order_Invoice, etc.)
 * - "document" means the PDF representation of a source that serves as input for the generator
 */
abstract class Vianetz_Pdf_Model_Generator_Abstract implements Vianetz_Pdf_Model_Generator_Interface
{
    /**
     * @var Varien_Object
     */
    private $initialEnvironment;

    /**
     * @var Mage_Core_Model_App_Emulation
     */
    private $appEmulation;

    /**
     * @var string
     */
    protected $pdfSize;

    /**
     * @var string
     */
    protected $pdfOrientation;

    /**
     * @var string
     */
    protected $pdfTitle;

    /**
     * @var string
     */
    protected $pdfAuthor;

    /**
     * @var boolean
     */
    protected $isDebugMode = false;

    /**
     * Constructor initializes configuration values.
     *
     * @param string $pdfSize
     * @param string $pdfOrientation
     * @param string $pdfTitle
     * @param string $pdfAuthor
     * @param boolean $isDebugMode
     */
    public function __construct($pdfSize, $pdfOrientation, $pdfTitle, $pdfAuthor, $isDebugMode = false)
    {
        $this->pdfSize = $pdfSize;
        $this->pdfOrientation = $pdfOrientation;
        $this->pdfTitle = $pdfTitle;
        $this->pdfAuthor = $pdfAuthor;
        $this->isDebugMode = $isDebugMode;

        $this->initGenerator();
    }

    /**
     * Initialize the generator instance.
     *
     * @return Vianetz_Pdf_Model_Generator_Abstract
     */
    protected function initGenerator()
    {
        // By default we do nothing..
        return $this;
    }

    /**
     * Replace special characters for DomPDF library.
     *
     * @param string $htmlContents
     *
     * @return string
     */
    protected function replaceSpecialChars($htmlContents)
    {
        // Nothing to do at the moment.

        return $htmlContents;
    }

    /**
     * Start store emulation for given store id.
     *
     * The translation is taken from the locale that is configured for the StoreView! (see System > Configuration > General > Locale)
     *
     * @param int $storeId
     *
     * @return $this
     */
    protected function startStoreEmulation($storeId)
    {
        // Basically what we do here is to emulate frontend so that we do not have to have duplicate templates
        // in frontend and adminhtml that contains the same data.
        $appEmulation = Mage::getSingleton('core/app_emulation');

        $this->initialEnvironment = $appEmulation->startEnvironmentEmulation($storeId);
        Mage::getResourceSingleton('catalog/product_flat')->setStoreId($storeId);

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate')
            ->setTranslateInline(false)
            ->init(Mage_Core_Model_App_Area::AREA_ADMINHTML, true);

        $this->appEmulation = $appEmulation;

        return $this;
    }

    /**
     * Stop currently running store emulation started by startStoreEmulation().
     *
     * @return $this
     */
    protected function stopStoreEmulation()
    {
        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');

        $this->appEmulation->stopEnvironmentEmulation($this->initialEnvironment);
        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Write the given string to debug file.
     *
     * @param string $fileContents
     *
     * @return boolean
     */
    protected function writeDebugFile($fileContents)
    {
        if ($this->isDebugMode === false) {
            return false;
        }

        $debugFileName = Mage::getBaseDir('tmp') . DS . 'debug_invoice.html';

        return (@file_put_contents($debugFileName, $fileContents) !== false);
    }
}
