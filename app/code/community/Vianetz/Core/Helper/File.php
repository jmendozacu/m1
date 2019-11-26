<?php
/**
 * File file helper class
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
 * @package     Vianetz_Core
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 */
class Vianetz_Core_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * Remove each of the files specified in the file name array.
     *
     * @param array $fileNameArray the files to remove
     *
     * @return Vianetz_Core_Helper_File
     */
    public function removeFiles(array $fileNameArray)
    {
        try {
            foreach ($fileNameArray as $fileName) {
                $test = new Varien_Io_File();
                $test->rm($fileName);
            }
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        return $this;
    }

    /**
     * Return relative path to media/ directory.
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getRelativeMediaPath($fileName)
    {
        return str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), Mage_Core_Model_Store::URL_TYPE_MEDIA . '/', $fileName);
    }
}
