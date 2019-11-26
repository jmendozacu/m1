<?php
/**
 * AdvancedInvoiceLayout tax helper class
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
class Vianetz_AdvancedInvoiceLayout_Helper_Tax extends Mage_Core_Helper_Abstract
{
    /**
     * Get calculated taxes for each tax class.
     *
     * This method is necessary as a wrapper since getCalculatedTaxes() method only exists since Magento 1.7.x.
     * As in Magento 1.7.x this method does NOT include shipping tax amount we can only use it since Magento 1.8.x.
     *
     * @see Mage_Sales_Model_Order_Pdf_Total_Default::getFullTaxInfo()
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getCalculatedTaxes(Mage_Sales_Model_Order $order)
    {
        if (version_compare(Mage::getVersion(), '1.8.1.0') >= 0) {
            $calculatedTaxes = Mage::helper('tax')->getCalculatedTaxes($order);

            if (empty($calculatedTaxes) === true) {
                return $this->_getFallbackCalculatedTaxes($order);
            }

            // Add shipping and handling tax percent if not available
            foreach ($calculatedTaxes as &$calculatedTax) {
                if ($calculatedTax['title'] === Mage::helper('tax')->__('Shipping & Handling Tax') && $calculatedTax['percent'] === null) {
                    $taxRateId = Mage::getSingleton('tax/config')->getShippingTaxClass($order->getStore());
                    $calculatedTax['percent'] = $this->_calculateTaxPercent($taxRateId, $order->getStore());
                }
            }

            return $calculatedTaxes;
        }

        return $this->_getFallbackCalculatedTaxes($order);
    }

    /**
     * Return shipping tax amount.
     *
     * This method is necessary as a wrapper since getCalculatedTaxes() method only exists since Magento 1.7.x.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function getShippingTax($order)
    {
        if (version_compare(Mage::getVersion(), '1.7.0.1') >= 0) {
            return Mage::helper('tax')->getShippingTax($order);
        }

        return array();
    }

    /**
     * Get full rate info
     *
     * @return array
     */
    protected function _getFullRateInfo(Mage_Sales_Model_Order $order)
    {
        $rates = Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($order)->toArray();
        $fullInfo = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
        return $fullInfo;
    }

    /**
     * Calculate the tax percentage for the given store and tax rate id.
     *
     * @param integer $taxRateId
     * @param Mage_Core_Model_Store $store
     *
     * @return integer
     */
    protected function _calculateTaxPercent($taxRateId, Mage_Core_Model_Store $store)
    {
        /** @var Mage_Tax_Model_Calculation $taxCalculation */
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);

        return $taxCalculation->getRate($request->setProductClassId($taxRateId));
    }

    /**
     * @param \Mage_Sales_Model_Order $order
     *
     * @return array
     */
    protected function _getFallbackCalculatedTaxes(Mage_Sales_Model_Order $order)
    {
        $totalsInfo = $this->_getFullRateInfo($order);

        $taxClassAmount = array();
        if (empty($totalsInfo) === true) {
            $totalsInfo = array(
                array(
                    'title' => Mage::helper('tax')->__('Tax'),
                    'tax_amount' => $order->getTaxAmount()
                )
            );
        }

        foreach ($totalsInfo as $info) {
            if (isset($info['hidden']) && $info['hidden']) {
                continue;
            }

            foreach ($info['rates'] as $rate) {
                $taxClassAmount[] = array(
                    'title' => $rate['title'],
                    'tax_amount' => $info['amount'],
                    'percent' => $rate['percent']
                );
            }
        }

        return $totalsInfo;
    }
}
