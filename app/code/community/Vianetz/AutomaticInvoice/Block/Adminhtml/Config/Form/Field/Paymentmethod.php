<?php
/**
 * Payment Method Dropdown Renderer
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

class Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Paymentmethod
    extends Mage_Core_Block_Html_Select
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        $this->setOptions(array());

        $options = Mage::getSingleton('adminhtml/system_config_source_payment_allowedmethods')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    /**
     * @param string $value
     *
     * @return Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Paymentmethod
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}