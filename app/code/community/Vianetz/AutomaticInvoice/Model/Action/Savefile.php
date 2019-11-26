<?php
/**
 * AutomaticInvoice Save to File Action Class
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
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
class Vianetz_AutomaticInvoice_Model_Action_Savefile extends Vianetz_AutomaticInvoice_Model_Action_Abstract
{
    /**
     * Check whether the action is allowed to be executed or not.
     *
     * @return boolean
     */
    public function canRun()
    {
        if (Mage::getStoreConfigFlag('automaticinvoice/' . $this->getSourceType() . '/savepdf') === false) {
            return false;
        }

        if (is_dir($this->getTargetDirectory()) === false || is_writable($this->getTargetDirectory()) === false) {
            Mage::helper('automaticinvoice')->log('The target directory ' . $this->getTargetDirectory() . ' does not exist or is not writable. Please create it manually.');
            return false;
        }

        return true;
    }

    /**
     * Save the invoice/shipment to the file system.
     *
     * If this method recognizes that our AdvancedInvoiceLayout extension (www.vianetz.com/advancedinvoicelayout) is
     * installed it will use its method to generate and save file into file system.
     *
     * @return Vianetz_AutomaticInvoice_Model_Action_Savefile
     */
    public function run()
    {
        if ($this->canRun() === false) {
            return $this;
        }

        if ($this->getHelper()->isUseAdvancedInvoiceLayoutExtension() === true) {
            $this->getHelper()->log('Advanced Invoice Layout extension has been recognized, using its pdf engine.');

            $filename = Mage::helper('advancedinvoicelayout')->getFilenameForSource($this->sourceModel, $this->getSourceType());

            Mage::getModel('advancedinvoicelayout/pdf')
                ->addSource($this->sourceModel)
                ->saveToFile($this->getTargetDirectory() . DS . $filename);
        } else {
            $filename = $this->getHelper()->stringToFilename('INV-' . $this->sourceModel->getIncrementId() . '.pdf');
            Mage::getModel('sales/order_pdf_' . $this->getSourceType())
                ->getPdf(array($this->sourceModel))
                ->save($this->getTargetDirectory() . DS . $filename);
        }

        $this->getHelper()->log('Saved ' . $this->getSourceType() . ' to file system: ' . $this->getTargetDirectory() . DS . $filename);

        return $this;
    }

    /**
     * Return the directory to save the file to.
     *
     * @return string
     */
    private function getTargetDirectory()
    {
        return Mage::getBaseDir('media') . DS . $this->getSourceType() . 's';
    }

    /**
     * @return \Vianetz_AutomaticInvoice_Helper_Data
     */
    private function getHelper()
    {
        return Mage::helper('automaticinvoice');
    }
}