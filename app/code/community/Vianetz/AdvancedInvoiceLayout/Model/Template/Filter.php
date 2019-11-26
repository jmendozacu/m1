<?php
/**
 * AdvancedInvoiceLayout Variable Filter model
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
class Vianetz_AdvancedInvoiceLayout_Model_Template_Filter extends Varien_Filter_Template
{
    /**
     * @var null|Mage_Sales_Model_Abstract
     */
    protected $_source = null;

    /**
     * @return Vianetz_AdvancedInvoiceLayout_Model_Template_Filter
     */
    public function init()
    {
        $this->_initVariables();

        return $this;
    }
    /**
     * @param $source
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Template_Filter
     */
    public function setSource($source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * @return null|Mage_Sales_Model_Abstract
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Add support for {{date..}} constructs.
     *
     * @see Varien_Filter_Template
     *
     * @param array $construction Array containing the whole string, and the splitted parts like command (date) and parameters (+14)
     *
     * @return string the formatted date string
     */
    public function dateDirective($construction)
    {
        if (count($construction) != 3) {
            return '';
        }

        $dateExpression = sprintf('%s %s days', $this->getSource()->getCreatedAt(), $construction[2]);
        return Mage::helper('core')->formatDate($dateExpression);
    }

    /**
     * Initialize all variables that can be accessed in the various free text fields and footer.
     *
     * These variable names should not change.
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Template_Filter
     */
    protected function _initVariables()
    {
        $variablesArray = array(
            'is_invoice' => ($this->getSource() instanceof Mage_Sales_Model_Order_Invoice),
            'is_shipment' => ($this->getSource() instanceof Mage_Sales_Model_Order_Shipment),
            'is_creditmemo' => ($this->getSource() instanceof Mage_Sales_Model_Order_Creditmemo),
            'increment_id' => $this->getSource()->getIncrementId(),
            'customer_id' => $this->getSource()->getCustomerId(),
            'order_increment_id' => $this->getSource()->getOrder()->getIncrementId(),
            'order_date' => $this->getSource()->getOrder()->getCreatedAtDate(),
            'payment_method' => $this->getSource()->getOrder()->getPayment()->getMethodInstance(),
            'prefix' => $this->getSource()->getOrder()->getCustomerPrefix(),
            'firstname' => $this->getSource()->getOrder()->getCustomerFirstname(),
            'lastname' => $this->getSource()->getOrder()->getCustomerLastname(),
            'shipping_date' => Mage::helper('advancedinvoicelayout/order')->getShippingDate($this->getSource()->getOrder()),

            // The following values are available with FireGento_MageSetup extension
            'merchant_tax_number' => Mage::getStoreConfig('general/imprint/tax_number'),
            'merchant_vat_number' => Mage::getStoreConfig('general/imprint/vat_id'),
            'bank_account' => Mage::getStoreConfig('general/imprint/bank_account'),
            'bank_code_number' => Mage::getStoreConfig('general/imprint/bank_code_number'),
            'bank_name' => Mage::getStoreConfig('general/imprint/bank_name'),
            'swift' => Mage::getStoreConfig('general/imprint/swift'),
            'iban' => Mage::getStoreConfig('general/imprint/iban')
        );

        $addresses = array(
            'billing' => $this->getSource()->getBillingAddress(),
            'shipping' => $this->getSource()->getShippingAddress()
        );

        foreach ($addresses as $addressType => $address) {
            /** @var Mage_Sales_Model_Order_Address|false $address */
            if (empty($address) === false && $address instanceof Mage_Sales_Model_Order_Address) {
                $variablesArray[$addressType . '_address'] = $address->format('txt');
            } else {
                $variablesArray[$addressType . '_address'] = '';
            }
        }

        // We add the payment method code as variable so that we can use e.g. something like this
        // {{depend is_payment_method_purchaseorder}}print something{{/depend}}
        $variablesArray['payment_method_' . $this->getSource()->getOrder()->getPayment()->getMethodInstance()->getCode()] = $this->getSource()->getOrder()->getPayment()->getMethodInstance()->getTitle();

        $this->setVariables($variablesArray);

        return $this;
    }
}
