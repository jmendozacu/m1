<?php
/**
 * AdvancedInvoiceLayout Customer Group Form Block
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
class Vianetz_AdvancedInvoiceLayout_Block_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{
    /**
     * Maximum text length for free text. Do not forget to adapt database column.
     *
     * @var int
     */
    const CUSTOMER_GROUP_FREETEXT_MAX_LENGTH = 500;

    /**
     * Overwrite original method to add new customer group specific free text field for invoices.
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Mage_Customer_Model_Group $customerGroup */
        $customerGroup = Mage::registry('current_group');
        $customerGroupId = $customerGroup->getId();

        // We only show the field on update because otherwise the observer cannot handle save.
        if (empty($customerGroupId)) {
            return;
        }

        $fieldset = $this->getForm()->addFieldset(
            'vianetz_advancedinvoicelayout_fieldset',
            array('legend' => Mage::helper('advancedinvoicelayout')->__('AdvancedInvoiceLayout Settings'))
        );

        $validateClass = sprintf('validate-length maximum-length-%d', self::CUSTOMER_GROUP_FREETEXT_MAX_LENGTH);
        $fieldset->addField(
            'vianetz_advancedinvoicelayout_customer_group_freetext',
            'textarea',
            array(
                'name'  => 'vianetz_advancedinvoicelayout_customer_group_freetext',
                'label' => Mage::helper('customer')->__('Group Specific Free Text on PDF Invoice'),
                'title' => Mage::helper('customer')->__('Group Specific Free Text on PDF Invoice'),
                'note'  => Mage::helper('customer')->__('This text will be displayed on PDF invoice if customer is assigned to this customer group. Max. %s characters.', self::CUSTOMER_GROUP_FREETEXT_MAX_LENGTH),
                'class' => $validateClass,
                'required' => false
            )
        );

        // Set field values.
        if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
            $this->getForm()->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $this->getForm()->addValues($customerGroup->getData());
        }
    }
}
