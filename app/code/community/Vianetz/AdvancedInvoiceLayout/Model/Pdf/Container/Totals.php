<?php
/**
 * AdvancedInvoiceLayout PDF Totals model
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
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Totals
 * @method Mage_Sales_Model_Abstract getSource()
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Totals extends Mage_Core_Model_Abstract
{
    /**
     * Default total model
     *
     * @var string
     */
    protected $defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * @var array
     */
    protected $fullTaxInfo;

    /**
     * Return source shipping amount value.
     *
     * The amount is including tax if the configuration in System > Configuration > Tax > Orders, Invoices, Credit Memos Display Settings > Display Shipping Amount
     * is set to "Including Tax".
     *
     * @return float
     */
    public function getShippingTotal()
    {
        $shippingInclTax = $this->getSource()->getShippingInclTax();
        $isShippingInclTax = Mage::getSingleton('tax/config')->displaySalesShippingInclTax($this->getSource()->getOrder()->getStoreId());
        if ((float)$shippingInclTax > 0 && $isShippingInclTax === true) {
            return $shippingInclTax;
        }

        return $this->getShippingTotalExclTax();
    }

    /**
     * Return source shipping amount value excluding tax.
     *
     * @return float
     */
    public function getShippingTotalExclTax()
    {
        return $this->getSource()->getShippingAmount();
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
        $totalValues = array();
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();

        foreach ($totals as $totalName => $totalInfo) {
            if (in_array($totalName, $this->_getExcludeTotalsFromOtherValues()) === true) {
                continue;
            }

            if (empty($totalInfo['model']) === false) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModel->setOrder($this->getSource()->getOrder())->setSource($this->getSource());

            if ($totalModel->canDisplay() == false) {
                continue;
            }

            foreach ($totalModel->getTotalsForDisplay() as $totalData) {
                $totalValues[$totalData['label']] = $totalData['amount'];
            }
        }

        return $totalValues;
    }

    /**
     * Return all tax values.
     *
     * Intentionally there is no check for Mage::getSingleton('tax/config')->displaySalesTaxWithGrandTotal() as in the Magento core
     * because most customers do not know and use this.
     *
     * @return array
     */
    public function getTaxTotalValues()
    {
        $isShowTaxes = Mage::getStoreConfigFlag('advancedinvoicelayout/general/show_taxes', $this->getSource()->getOrder()->getStore());
        if ($isShowTaxes === false) {
            return array();
        }

        $isShowTaxRateName = Mage::getStoreConfigFlag('advancedinvoicelayout/general/show_taxrate_name');

        $totalArray = array();

        $isDisplayFullSummary = Mage::getSingleton('tax/config')->displaySalesFullSummary($this->getSource()->getOrder()->getStore());
        if ($isDisplayFullSummary === false) {
            $taxLabel = Mage::helper('advancedinvoicelayout')->__('Tax');
            // $this->formatPrice() is called in totals block itself
            $totalArray[$taxLabel] = $this->getSource()->getTaxAmount();

            return $totalArray;
        }

        foreach ($this->getFullTaxInfo() as $taxRateData) {
            // We use the provided title by default (e.g. "Shipping & Handling Tax").
            $taxLabel = $taxRateData['id'];

            if ($isShowTaxRateName === false && isset($taxRateData['percent']) === true && $taxRateData['percent'] > 0) {
                $taxLabel = Mage::helper('advancedinvoicelayout')->__('%g%% tax', $taxRateData['percent']);
            }

            $taxDisplayType = Mage::getStoreConfig('advancedinvoicelayout/general/tax_display_type', $this->getSource()->getOrder()->getStore());
            if ($taxDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX) {
                $taxLabel = Mage::helper('advancedinvoicelayout')->__('incl.') . ' ' . $taxLabel;
            }

            if (isset($totalArray[$taxLabel]) === true) {
                $totalArray[$taxLabel] += $taxRateData['amount'];
            } else {
                $totalArray[$taxLabel] = $taxRateData['amount'];
            }
        }

        return $totalArray;
    }

    /**
     * Retrieve detailed tax information for the tax table.
     *
     * @return array
     */
    public function getTaxPercentValues()
    {
        $itemValueExclTaxByTaxPercent = array();
        $itemValueInclTaxByTaxPercent = array();
        foreach ($this->getSource()->getOrder()->getAllItems() as $item) {
            $itemContainer = Mage::getModel('advancedinvoicelayout/pdf_container_item_default');

            $taxPercentValue = sprintf('%g', $itemContainer->getItemTaxPercent($item));

            if (empty($itemValueExclTaxByTaxPercent[$taxPercentValue]) === true) {
                $itemValueExclTaxByTaxPercent[$taxPercentValue] = 0;
            }

            if (empty($itemValueInclTaxByTaxPercent[$taxPercentValue]) === true) {
                $itemValueInclTaxByTaxPercent[$taxPercentValue] = 0;
            }

            $itemValueExclTaxByTaxPercent[$taxPercentValue] += $itemContainer->getItemRowTotal($item, Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX);
            $itemValueInclTaxByTaxPercent[$taxPercentValue] += $itemContainer->getItemRowTotal($item, Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX);
        }

        $taxPercentValues = array();
        foreach ($this->getFullTaxInfo() as $taxRateData) {
            if ($taxRateData['percent'] <= 0) {
                continue;
            }

            $taxPercentValue = $taxPercentValue = sprintf('%g', $taxRateData['percent']);

            $merchandiseValueExclTax = 0;
            if (empty($itemValueExclTaxByTaxPercent[$taxPercentValue]) === false) {
                $merchandiseValueExclTax = $itemValueExclTaxByTaxPercent[$taxPercentValue];
            }

            $merchandiseValueInclTax = 0;
            if (empty($itemValueInclTaxByTaxPercent[$taxPercentValue]) === false) {
                $merchandiseValueInclTax = $itemValueInclTaxByTaxPercent[$taxPercentValue];
            }

            $taxPercentValues[$taxPercentValue] = array(
                'title' => Mage::helper('advancedinvoicelayout')->__('%g%%', $taxRateData['percent']),
                'tax_value' => $taxRateData['amount'],
                'merchandise_value_excl_tax' => $merchandiseValueExclTax,
                'merchandise_value_incl_tax' => $merchandiseValueInclTax
            );
        }

        return $taxPercentValues;
    }

    /**
     * Get full information about taxes applied to order.
     *
     * Unfortunately we cannot use $this->getOrder()->getFullTaxInfo() because the value might be added to the grand total
     * and therewith it is not always possible to get only the tax value.
     *
     * @see Mage_Adminhtml_Block_Sales_Order_Totals_Tax::getFullTaxInfo() (for display in admin backend)
     *
     * @return array
     */
    protected function getFullTaxInfo()
    {
        if (empty($this->fullTaxInfo) === true) {
            $order = $this->getSource()->getOrder();
            if (!$order instanceof Mage_Sales_Model_Order) {
                return array();
            }

            $fullTaxInfoArray = array();
            $calculatedTaxes = Mage::helper('advancedinvoicelayout/tax')->getCalculatedTaxes($order);
            foreach ($calculatedTaxes as $taxRate) {
                $fullTaxInfoArray[] = array(
                    'percent' => $taxRate['percent'],
                    'amount' => $taxRate['tax_amount'],
                    'id' => $taxRate['title']
                );
            }
            $this->fullTaxInfo = $fullTaxInfoArray;
        }

        return $this->fullTaxInfo;
    }

    /**
     * Get total codes that must not be contained in the output of $this::getOtherTotalValues() because it was
     * handled separately by its own method, e.g. getSubtotal(), getShippingTotal(), etc.
     *
     * @return array
     */
    protected function _getExcludeTotalsFromOtherValues()
    {
        return array(
            'subtotal', 'discount', 'shipping', 'grand_total', 'tax'
        );
    }
}
