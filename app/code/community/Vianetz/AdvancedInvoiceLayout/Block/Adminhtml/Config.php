<?php
/**
 * AdvancedInvoiceLayout Config Block
 *
 * NOTICE
 * Magento 1.4.x has a bug with the prepareShipment() function in app/code/core/Mage/Sales/Model/Order.php
 * (Please apply the patch mentioned in http://magebase.com/magento-tutorials/shipment-api-in-magento-1-4-1-broken/)s
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
class Vianetz_AdvancedInvoiceLayout_Block_Adminhtml_Config extends Vianetz_Core_Block_Config
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        if ($this->helper('core')->isModuleEnabled('Vianetz_AutomaticInvoice') === false) {
            $html .= '<ul class="messages"><li class="notice-msg"><ul><li>';
            $html .= $this->helper('advancedinvoicelayout')->__('Do you already know our <a href="%s" target="_blank">AutomaticInvoice</a> extension for automatically generating invoices and shipments depending on certain order status?', 'http://www.vianetz.com/automaticinvoice?utm_source=magentobackend&utm_medium=postrequest&utm_campaign=CrosssellAutomaticInvoice');
            $html .= '</li></ul></li></ul>';
        }

        return $html;
    }
}