<?php
/**
 * AdvancedInvoiceLayout general helper class
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
class Vianetz_AdvancedInvoiceLayout_Model_Source_Type extends Mage_Core_Model_Abstract
{
    /**
     * @var string
     */
    const SOURCE_TYPE_INVOICE = 'invoice';

    /**
     * @var string
     */
    const SOURCE_TYPE_SHIPMENT = 'shipment';

    /**
     * @var string
     */
    const SOURCE_TYPE_CREDITMEMO = 'creditmemo';

    /**
     * Map source to source type.
     *
     * @param Mage_Sales_Model_Abstract $source
     *
     * @return string
     * @throws Vianetz_AdvancedInvoiceLayout_Exception
     */
    public function getTypeBySource(Mage_Sales_Model_Abstract $source)
    {
        if ($source instanceof Mage_Sales_Model_Order_Invoice) {
            return self::SOURCE_TYPE_INVOICE;
        } elseif ($source instanceof Mage_Sales_Model_Order_Shipment) {
            return self::SOURCE_TYPE_SHIPMENT;
        } elseif ($source instanceof Mage_Sales_Model_Order_Creditmemo) {
            return self::SOURCE_TYPE_CREDITMEMO;
        }

        throw new Vianetz_AdvancedInvoiceLayout_Exception('Invalid source specified');
    }
}
