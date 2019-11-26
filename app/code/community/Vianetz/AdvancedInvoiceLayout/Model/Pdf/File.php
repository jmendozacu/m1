<?php
/**
 * AdvancedInvoiceLayout File Model
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

class Vianetz_AdvancedInvoiceLayout_Model_Pdf_File
{
    /**
     * Return filename with configured prefix for given source (invoice/shipment/creditmemo).
     *
     * @param Mage_Sales_Model_Abstract|Mage_Sales_Model_Resource_Order_Collection_Abstract $source The source model.
     * @param string $configSection Config section to check for.
     *
     * @return string
     */
    public function getFilenameForSource($source, $configSection)
    {
        $storeId = Mage::app()->getStore()->getId();
        if ($source instanceof Mage_Sales_Model_Abstract) {
            $suffix = $source->getIncrementId();
            $storeId = $source->getStoreId();
        } elseif ($source instanceof Mage_Sales_Model_Resource_Order_Collection_Abstract) {
            if ($source->getFirstItem()->getIncrementId() == $source->getLastItem()->getIncrementId()) {
                $suffix = $source->getFirstItem()->getIncrementId();
            } elseif (count($source) > 1) {
                $suffix = $source->getFirstItem()->getIncrementId() . '-' . $source->getLastItem()->getIncrementId();
            }
        } else {
            $suffix = $source;
        }

        $configuredFileName = Mage::getStoreConfig('advancedinvoicelayout/' . $configSection . '/file_name', $storeId);
        if (empty($configuredFileName) === false) {
            $fileName = $configuredFileName;
        } else {
            $fileName = (string)Mage::helper('advancedinvoicelayout')->__($configSection);
        }

        return $this->__getExportDirectory($configSection) . DS . $fileName . '_' . $suffix . '.pdf';
    }

    private function __getExportDirectory($documentType)
    {
        return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . Mage::getStoreConfig('advancedinvoicelayout/' . $documentType . '/storage_folder');
    }
}