<?php
/**
 * AutomaticInvoice Config Block
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
class Vianetz_AutomaticInvoice_Block_Adminhtml_Config extends Vianetz_Core_Block_Config
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        if (Mage::helper('core')->isModuleEnabled('Vianetz_AdvancedInvoiceLayout') === false) {
            $html .= '<ul class="messages"><li class="notice-msg"><ul><li>';
            $html .= $this->helper('automaticinvoice')->__('Do you already know our <a href="%s" target="_blank">AdvancedInvoiceLayout</a> extension for making your Magento PDF invoices looking nice?', 'http://www.vianetz.com/advancedinvoicelayout?utm_source=magentobackend&utm_medium=postrequest&utm_campaign=CrosssellAdvancedInvoiceLayout');
            $html .= '</li></ul></li></ul>';
        }

        return $html;
    }
}