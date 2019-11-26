<?php
/**
 * AutomaticInvoice Abstract Save Order Workflow Class
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
abstract class Vianetz_AutomaticInvoice_Model_Workflow_Order_Save_Abstract
{
    /**
     * Return the source type, e.g. invoice or shipment.
     *
     * @return string
     */
    abstract public function getSourceType();

    /**
     * This method checks if saving is allowed and then triggers the specific source model pdf save.
     *
     * @param Mage_Sales_Model_Abstract $sourceModel invoice/shipment instance
     *
     * @return bool
     */
    public function start(Mage_Sales_Model_Abstract $sourceModel)
    {
        if ($this->isEnabled($sourceModel->getStore()) !== true) {
            return false;
        }

        try {
            Mage::getModel('automaticinvoice/action_savefile')
                ->setSourceModel($sourceModel)
                ->run();
        } catch (Exception $exception) {
            Mage::helper('automaticinvoice')->log('Error occurred while saving ' . $this->getSourceType() . ' to file system: ' . $exception->getMessage() . $exception->getTraceAsString());
        }

        return true;
    }

    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return bool
     */
    protected function isEnabled(Mage_Core_Model_Store $store)
    {
        return Mage::getStoreConfigFlag('automaticinvoice/' . $this->getSourceType() . '/savepdf', $store);
    }
}