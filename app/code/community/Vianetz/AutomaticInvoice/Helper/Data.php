<?php
/**
 * AutomaticInvoice Helper Class
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
class Vianetz_AutomaticInvoice_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return active payment methods.
     *
     * @return array
     */
    public function getActivePaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        $methods = array(array('value'=>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }

    /**
     * Sanitize string to filename by removing special chars.
     *
     * @param string $filename
     *
     * @return string
     */
    public function stringToFilename($filename)
    {
        $dangerousChars = array(' ', '"', "'", '&', "/", "\\", "?", "#", DS, ':');

        return str_replace($dangerousChars, '_', $filename);
    }

    /**
     * Log message to file if enabled in system configuration.
     *
     * @param string $message
     * @param int $type
     *
     * @return Vianetz_AutomaticInvoice_Helper_Data
     */
    public function log($message, $type = LOG_DEBUG)
    {
        $isLoggingEnabled = Mage::getStoreConfigFlag('automaticinvoice/general/log_enabled');
        if ($isLoggingEnabled === false) {
            return $this;
        }

        Mage::helper('vianetz_core/log')->log($message, $type, 'Vianetz_AutomaticInvoice');
        return $this;
    }

    /**
     * Check whether the AdvancedInvoiceLayout v2 is installed and should be used.
     *
     * @return boolean
     */
    public function isUseAdvancedInvoiceLayoutExtension()
    {
        $version = Mage::getConfig()->getModuleConfig('Vianetz_AdvancedInvoiceLayout')->version;

        if (Mage::helper('core')->isModuleEnabled('Vianetz_AdvancedInvoiceLayout') === false) {
            return false;
        }

        if (preg_match('/2\..*/', $version) !== 1) {
            return false;
        }

        if (Mage::helper('advancedinvoicelayout')->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return false;
        }

        return true;
    }
}