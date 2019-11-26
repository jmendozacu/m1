<?php
/**
 * FPDI Merger class
 *
 * This class is responsible for merging individual PDF documents and eventually adding the background PDF template file.
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

require_once('fpdf-advancedinvoicelayout' . DS . 'fpdf.php');

// We include everything here to avoid Magento autoloader warnings of the external libraries in the log.
// The reverse order is important here.
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'fpdi_bridge.php');
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'fpdf_tpl.php');
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'pdf_parser.php');
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'fpdi_pdf_parser.php');
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'pdf_context.php');
require_once('SetasignFpdi-advancedinvoicelayout' . DS . 'fpdi.php');

/**
 * Class Vianetz_Pdf_Model_Merger_Fpdi
 */
final class Vianetz_Pdf_Model_Merger_Fpdi extends Vianetz_Pdf_Model_Merger_Abstract
{
    /**
     * @var string
     */
    const OUTPUT_MODE_STRING = 'S';

    /**
     * @var string
     */
    const OUTPUT_FORMAT_LANDSCAPE = 'L';

    /**
     * @var string
     */
    const OUTPUT_FORMAT_PORTRAIT = 'P';

    /**
     * The FPDI model instance.
     *
     * @var FPDI
     */
    private $fpdiModel;

    /**
     * Default constructor initializes the FPDI library.
     */
    public function __construct()
    {
        $this->fpdiModel = new FPDI();
    }

    /**
     * Import the specified page number from the given file into the current pdf model.
     *
     * @param string $fileName
     * @param int $pageNumber
     *
     * @return Vianetz_Pdf_Model_Merger_Fpdi
     */
    public function importPageFromFile($fileName, $pageNumber)
    {
        $this->fpdiModel->setSourceFile($fileName);
        $pageId = $this->fpdiModel->importPage($pageNumber);

        $this->fpdiModel->useTemplate($pageId);

        return $this;
    }

    /**
     * @return string
     */
    public function getPdfContents()
    {
        return $this->fpdiModel->Output(self::OUTPUT_MODE_STRING);
    }

    /**
     * @param string $fileName
     *
     * @return integer
     */
    public function countPages($fileName)
    {
        return $this->fpdiModel->setSourceFile($fileName);
    }

    /**
     * @return Vianetz_Pdf_Model_Merger_Fpdi
     */
    public function addPage()
    {
        $this->fpdiModel->addPage();

        return $this;
    }
}
