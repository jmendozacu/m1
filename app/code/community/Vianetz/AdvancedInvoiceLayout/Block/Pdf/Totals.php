<?php
/**
 * AdvancedInvoiceLayout Pdf Invoice Footer block
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

/**
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Totals
 * @method Mage_Sales_Model_Abstract getSource()
 */
class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Totals extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract
{
    /**
     * @var Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Totals
     */
    protected $_totalsModel;

    /**
     * Init template.
     */
    protected function _construct()
    {
        $this->setTemplate($this->_getTemplateFilePath('totals.phtml'));
        $this->_totalsModel = Mage::getModel('advancedinvoicelayout/pdf_container_totals')->setSource($this->getSource());

        parent::_construct();
    }

    /**
     * Return source grand total value.
     *
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->getSource()->getGrandTotal();
    }

    /**
     * Return source grand total value excluding tax.
     *
     * @return float
     */
    public function getGrandTotalExclTax()
    {
        return $this->getGrandTotal() - $this->getSource()->getTaxAmount();
    }

    /**
     * Return source subtotal value.
     *
     * @return float
     */
    public function getSubTotal()
    {
        return $this->getSource()->getSubtotalInclTax();
    }

    /**
     * Return source subtotal value excluding tax.
     *
     * @return float
     */
    public function getSubTotalExclTax()
    {
        return $this->getSource()->getSubtotal();
    }

    /**
     * Return source shipping amount value.
     *
     * @return float
     */
    public function getShippingTotal()
    {
        return $this->_totalsModel->getShippingTotal();
    }

    /**
     * Return source discount total value.
     *
     * @return float
     */
    public function getDiscountTotal()
    {
        return $this->getSource()->getDiscountAmount();
    }

    /**
     * Return all tax values.
     *
     * Intentionally there is no check for Mage::getSingleton('tax/config')->displaySalesTaxWithGrandTotal() as in the Magento core
     * because most clients do not know and use this.
     *
     * @return array
     */
    public function getTaxTotalValues()
    {
        return $this->_totalsModel->getTaxTotalValues();
    }

    /**
     * Retrieve tax percent values for the tax table.
     *
     * @return array
     */
    public function getTaxPercentValues()
    {
        return $this->_totalsModel->getTaxPercentValues();
    }

    /**
     * Get all other total values that have not been handled separately by the methods above, e.g. getSubTotal(), getShippingTotal(), etc.
     *
     * @see Mage_Sales_Model_Order_Pdf_Abstract::_getTotalsList()
     * @see Mage_Sales_Model_Order_Pdf_Abstract::insertTotals()
     *
     * @return array
     */
    public function getOtherTotalValues()
    {
        return $this->_totalsModel->getOtherTotalValues();
    }

    /**
     * Check whether subtotal excluding tax should be displayed.
     *
     * @return boolean
     */
    public function isShowSubtotalExcludingTax()
    {
        if ($this->isShowTaxes() === false) {
            return false;
        }

        $taxDisplayType = Mage::getStoreConfig('advancedinvoicelayout/general/tax_display_type', $this->getOrder()->getStore());

        return ($taxDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX || $taxDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH);
    }

    /**
     * Check whether subtotal including tax should be displayed.
     *
     * @return boolean
     */
    public function isShowSubtotalIncludingTax()
    {
        if ($this->isShowTaxes() === false) {
            return false;
        }

        $taxDisplayType = Mage::getStoreConfig('advancedinvoicelayout/general/tax_display_type', $this->getOrder()->getStore());

        return ($taxDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX || $taxDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH);
    }

    /**
     * Check whether to display tax table (can be activated in configuration for invoices).
     *
     * @return boolean
     */
    public function isShowTaxTable()
    {
        return ($this->getSource() instanceof Mage_Sales_Model_Order_Invoice
            && Mage::getStoreConfigFlag('advancedinvoicelayout/invoice/show_tax_table', $this->getOrder()->getStore()));
    }
}
