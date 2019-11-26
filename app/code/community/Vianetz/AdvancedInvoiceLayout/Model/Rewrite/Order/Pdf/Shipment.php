<?php
/**
 * AdvancedInvoiceLayout Pdf Shipment rewrite model
 *
 * NOTE
 * This model only exists for compatibility reasons, i.e. other applications that require the sales/order_pdf_invoice model
 * to return the new content. It is not required for the sole AdvancedInvoiceLayout functionality.
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
class Vianetz_AdvancedInvoiceLayout_Model_Rewrite_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
    /**
     * Return the instantiated pdf model.
     *
     * @param array $shipments
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Pdf|Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
        if (Mage::helper('advancedinvoicelayout')->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return parent::getPdf($shipments);
        }

        /** @var Vianetz_AdvancedInvoiceLayout_Model_Pdf $pdfModel */
        $pdfModel = Mage::getModel('advancedinvoicelayout/pdf');

        foreach ($shipments as $shipment) {
            $pdfModel->addSource($shipment);
        }

        return $pdfModel;
    }
}
