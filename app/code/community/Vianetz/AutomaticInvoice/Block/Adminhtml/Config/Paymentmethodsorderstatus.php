<?php
/**
 * Config Section Model for Payment Methods and Order Status
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

class Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Paymentmethodsorderstatus
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Paymentmethod
     */
    protected $_paymentMethodRenderer;

    /**
     * @var Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Orderstatus
     */
    protected $_orderStatusRenderer;

    /**
     * @var Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected $_isGenerateRenderer;

    /**
     * @var Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected $_isTriggerEmailRenderer;

    /**
     * @var Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected $_isNotifyCustomerRenderer;

    /**
     * Render the table containing payment methods and related order status trigger.
     *
     * @return void
     */
    public function _prepareToRender()
    {
        $this->addColumn('order_status', array(
            'label' => Mage::helper('automaticinvoice')->__('Order Status'),
            'renderer' => $this->_getOrderStatusRenderer()
        ));
        $this->addColumn('payment_method', array(
            'label' => Mage::helper('automaticinvoice')->__('Payment Method'),
            'renderer' => $this->_getPaymentMethodRenderer()
        ));
        $this->addColumn('is_generate', array(
            'label' => Mage::helper('automaticinvoice')->__('Generate'),
            'renderer' => $this->_getIsGenerateRenderer()
        ));
        $this->addColumn('is_trigger_email', array(
            'label' => Mage::helper('automaticinvoice')->__('Trigger Email'),
            'renderer' => $this->_getIsTriggerEmailRenderer()
        ));
        $this->addColumn('is_notify_customer', array(
            'label' => Mage::helper('automaticinvoice')->__('Notify Customer'),
            'renderer' => $this->_getIsNotifyCustomerRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('automaticinvoice')->__('Add');
    }

    /**
     * Return the renderer for the payment method dropdown.
     *
     * @return Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Paymentmethod
     */
    protected function _getPaymentMethodRenderer()
    {
        if (!$this->_paymentMethodRenderer) {
            $this->_paymentMethodRenderer = $this->getLayout()->createBlock(
                'automaticinvoice/adminhtml_config_form_field_paymentmethod', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_paymentMethodRenderer;
    }

    /**
     * Return the renderer for the order status dropdown.
     *
     * @return Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Orderstatus
     */
    protected function _getOrderStatusRenderer()
    {
        if (!$this->_orderStatusRenderer) {
            $this->_orderStatusRenderer = $this->getLayout()->createBlock(
                'automaticinvoice/adminhtml_config_form_field_orderstatus', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_orderStatusRenderer;
    }

    /**
     * @return \Mage_Core_Block_Abstract|\Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected function _getIsGenerateRenderer()
    {
        if (!$this->_isGenerateRenderer) {
            $this->_isGenerateRenderer = $this->getLayout()->createBlock(
                'automaticinvoice/adminhtml_config_form_field_checkbox', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_isGenerateRenderer;
    }

    /**
     * @return \Mage_Core_Block_Abstract|\Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected function _getIsTriggerEmailRenderer()
    {
        if (!$this->_isTriggerEmailRenderer) {
            $this->_isTriggerEmailRenderer = $this->getLayout()->createBlock(
                'automaticinvoice/adminhtml_config_form_field_checkbox', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_isTriggerEmailRenderer;
    }

    /**
     * @return \Mage_Core_Block_Abstract|\Vianetz_AutomaticInvoice_Block_Adminhtml_Config_Form_Field_Checkbox
     */
    protected function _getIsNotifyCustomerRenderer()
    {
        if (!$this->_isNotifyCustomerRenderer) {
            $this->_isNotifyCustomerRenderer = $this->getLayout()->createBlock(
                'automaticinvoice/adminhtml_config_form_field_checkbox', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_isNotifyCustomerRenderer;
    }

    /**
     * Add the dropdowns for payment method and order status.
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getPaymentMethodRenderer()
                ->calcOptionHash($row->getData('payment_method')),
            'selected="selected"'
        );

        $row->setData(
            'option_extra_attr_' . $this->_getOrderStatusRenderer()
                ->calcOptionHash($row->getData('order_status')),
            'selected="selected"'
        );

        $row->setData(
            'checkbox_extra_attr_' . $this->_getIsGenerateRenderer()
                ->calcOptionHash($row->getData('is_generate')),
            'checked="checked"'
        );

        $row->setData(
            'checkbox_extra_attr_' . $this->_getIsTriggerEmailRenderer()
                ->calcOptionHash($row->getData('is_trigger_email')),
            'checked="checked"'
        );

        $row->setData(
            'checkbox_extra_attr_' . $this->_getIsNotifyCustomerRenderer()
                ->calcOptionHash($row->getData('is_notify_customer')),
            'checked="checked"'
        );
    }
}