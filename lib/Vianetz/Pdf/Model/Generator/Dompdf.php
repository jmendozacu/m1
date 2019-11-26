<?php
/**
 * DOMPdf generator class
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
 * Class Vianetz_Pdf_Model_Generator_Dompdf
 *
 * Known limitations of Dompdf:
 * - colspan is not working properly (table is moved to the top of the page)
 * - umlauts in Magento translated strings are not printed
 * - no CSS background images with relative paths
 * - dompdf doesn’t like all CSS shortcuts. In particular, font: 12pt Helvetica; and background: #999;
 *   didn’t work as well as explicitly setting the font-family and font-size separately, and setting the
 *   background-color.
 */
final class Vianetz_Pdf_Model_Generator_Dompdf extends Vianetz_Pdf_Model_Generator_Abstract
{
    /**
     * @var DOMPdf
     */
    private $domPdf;

    /**
     * Render the pdf document.
     *
     * @param Vianetz_Pdf_Model_Document_Interface $documentModel
     *
     * @return string
     */
    public function renderPdfDocument(Vianetz_Pdf_Model_Document_Interface $documentModel)
    {
        $this->initPdf();

        $this->domPdf->load_html($this->getHtmlContentsForDocument($documentModel));
        $this->domPdf->render();

        return $this->domPdf->output();
    }

    /**
     * Init PDF default settings.
     *
     * @return Vianetz_Pdf_Model_Generator_Dompdf
     */
    protected function initPdf()
    {
        $this->domPdf = new \Dompdf\Dompdf($this->getDompdfOptions());

        $this->domPdf->set_paper($this->pdfSize, $this->pdfOrientation);

        $this->domPdf->add_info('Author', $this->pdfAuthor);
        $this->domPdf->add_info('Title', $this->pdfTitle);

        $this->domPdf->set_base_path(Mage::getBaseDir() . DS);

        return $this;
    }

    /**
     * Return HTML contents for one single document that is later merged with the others.
     *
     * We emulate frontend store of the invoice/shipment/creditmemo so that we do not have to create templates
     * in both frontend and adminhtml directory. Furthermore we can print invoices in frontend and backend the same way.
     * This also implies that the localization is taken from the appropriate store.
     *
     * @param Vianetz_Pdf_Model_Document_Interface $documentModel
     *
     * @return string
     * @throws Exception
     */
    protected function getHtmlContentsForDocument(Vianetz_Pdf_Model_Document_Interface $documentModel)
    {
        $this->startStoreEmulation($documentModel->getStoreId());
        try {
            $htmlContents = $documentModel->getHtmlContents();
        } catch (Exception $ex) {
            $this->stopStoreEmulation();
            throw $ex;
        }

        $this->stopStoreEmulation();

        $htmlContents = $this->replaceSpecialChars($htmlContents);
        $this->writeDebugFile($htmlContents);

        return $htmlContents;
    }

    /**
     * @see \Dompdf\Options
     *
     * @return array
     */
    private function getDompdfOptions()
    {
        return array(
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'tempDir' => Mage::getBaseDir('tmp')
        );
    }
}
