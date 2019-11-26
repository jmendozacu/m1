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
 * Class Vianetz_Pdf_Model_Merger_Abstract
 */
abstract class Vianetz_Pdf_Model_Merger_Abstract implements Vianetz_Pdf_Model_Merger_Interface
{
    /**
     * Merge the specified PDF file into the current file.
     *
     * @param string $fileName
     * @param null|string $pdfBackgroundFile
     * @param null|string $pdfBackgroundFileForFirstPage
     *
     * @return Vianetz_Pdf_Model_Merger_Abstract
     */
    public function mergePdfFile($fileName, $pdfBackgroundFile = null, $pdfBackgroundFileForFirstPage = null)
    {
        if (empty($fileName) === true || file_exists($fileName) === false) {
            return $this;
        }

        for ($pageNumber = 1; $pageNumber <= $this->countPages($fileName); $pageNumber++) {
            $this->addPage();

            if ($pageNumber === 1 && empty($pdfBackgroundFileForFirstPage) === false) {
                $this->importBackgroundTemplateFile($pdfBackgroundFileForFirstPage);
            } elseif (empty($pdfBackgroundFile) === false) {
                $this->importBackgroundTemplateFile($pdfBackgroundFile);
            }

            $this->importPageFromFile($fileName, $pageNumber);
        }

        return $this;
    }


    /**
     * Add the background pdf (if enabled and file exists).
     *
     * @param string $pdfBackgroundFile
     *
     * @return Vianetz_Pdf_Model_Merger_Abstract
     */
    protected function importBackgroundTemplateFile($pdfBackgroundFile)
    {
        if (empty($pdfBackgroundFile) === true || file_exists($pdfBackgroundFile) === false) {
            return $this;
        }

        $this->importPageFromFile($pdfBackgroundFile, 1); // We assume the background pdf has only one page.

        return $this;
    }
}
