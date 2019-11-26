<?php
/**
 * Vianetz Public Pdf Model
 *
 * This class wraps all the internal processes and is the main class that is intended to be used by developers.
 * Usage:
 * 1) Instantiate (optionally with your custom generator class)
 * 2) addDocument($document)
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
 * @package     Vianetz_Pdf
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */

/**
 * Class Vianetz_Pdf_Model_Pdf
 */
class Vianetz_Pdf_Model_Pdf
{
    /**
     * The generator instance.
     *
     * @var Vianetz_Pdf_Model_Generator_Interface
     */
    private $generator;

    /**
     * @var Vianetz_Pdf_Model_Merger_Interface
     */
    private $merger;

    /**
     * The (cached) pdf contents.
     *
     * @var string
     */
    private $contents;

    /**
     * Initialize empty array for PDF documents to print.
     *
     * @var array
     */
    private $documents = array();

    /**
     * @var Vianetz_Pdf_Model_Config
     */
    protected $config;

    /**
     * Default constructor initializes pdf generator.
     *
     * A custom generator class may be injected via $this->setGenerator(), otherwise the default DomPdf generator is used.
     */
    final public function __construct()
    {
        // Initialize default configuration.
        $this->config = new Vianetz_Pdf_Model_Config();

        $this->init();

        // Set generator fixed to DomPdf at the moment.
        $this->generator = new Vianetz_Pdf_Model_Generator_Dompdf(
            $this->config->pdfSize,
            $this->config->pdfOrientation,
            $this->config->pdfTitle,
            $this->config->pdfAuthor,
            $this->config->isDebugMode
        );

        $this->merger = new Vianetz_Pdf_Model_Merger_Fpdi();
    }

    /**
     * @return Vianetz_Pdf_Model_Pdf
     */
    protected function init()
    {
        // By default nothing to do. You can e.g. overwrite the default configuration..
        return $this;
    }

    /**
     * Get pdf file contents as string.
     *
     * @api
     * @return string
     */
    final public function getContents()
    {
        if ($this->contents === null) {
            $this->contents = $this->renderPdfContentsForAllDocuments();
        }

        return $this->contents;
    }

    /**
     * Save pdf contents to file.
     *
     * @param string $fileName
     *
     * @api
     * @return boolean true in case of success
     */
    final public function saveToFile($fileName)
    {
        $pdfContents = $this->getContents();

        return (@file_put_contents($fileName, $pdfContents) !== false);
    }

    /**
     * Add a new document to generate.
     *
     * @param Vianetz_Pdf_Model_Document_Interface $documentModel
     *
     * @api
     * @return Vianetz_Pdf_Model_Pdf
     */
    final public function addDocument(Vianetz_Pdf_Model_Document_Interface $documentModel)
    {
        $this->documents[] = $documentModel;
        // Reset cached pdf contents.
        $this->contents = null;

        return $this;
    }

    /**
     * Returns the number of documents added to the generator.
     *
     * @return int
     */
    final public function countDocuments()
    {
        return count($this->documents);
    }

    /**
     * Render method for compatibility reasons.
     *
     * Note:
     * This method only exists for compatibility reasons to provide the same interface as the original Magento Zend_Pdf
     * components.
     *
     * @return string
     */
    final public function render()
    {
        return $this->getContents();
    }

    /**
     * Return merged pdf contents of all documents and save it to single temporary files.
     *
     * @return string
     */
    private function renderPdfContentsForAllDocuments()
    {
        $tmpFileNameArray = array();
        foreach ($this->documents as $documentInstance) {
            if (!$documentInstance instanceof Vianetz_Pdf_Model_Document_Interface) {
                continue;
            }

            $pdfContents = $this->generator->renderPdfDocument($documentInstance);
            $tmpFileName = $this->getTmpFilename();
            @file_put_contents($tmpFileName, $pdfContents);
            $tmpFileNameArray[] = $tmpFileName;

            $this->merger->mergePdfFile($tmpFileName, $documentInstance->getPdfBackgroundFile(), $documentInstance->getPdfBackgroundFileForFirstPage());
            $this->merger->mergePdfFile($documentInstance->getPdfAttachmentFile());

            Mage::dispatchEvent(
                'advancedinvoicelayout_pdf_' . $documentInstance->getDocumentType() . '_document_render_after',
                array('merger' => $this->merger, 'document' => $documentInstance)
            );
        }

        if (count($tmpFileNameArray) === 0) {
            Mage::throwException('No data to print.');
        }

        Mage::helper('vianetz_core/file')->removeFiles($tmpFileNameArray);

        return $this->merger->getPdfContents();
    }

    /**
     * Return temporary filename for merging.
     *
     * @return string
     */
    private function getTmpFilename()
    {
        return $this->config->tempDir . DS . uniqid(time()) . '.pdf';
    }
}
