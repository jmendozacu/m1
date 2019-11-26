<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Block_Adminhtml_Method_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /*$params = $this->getRequest()->getParams();*/
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('method_form', array('legend' => Mage::helper('caship')->__('Infomodus shipping method information')));

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => Mage::helper('caship')->__('Title'),
            'title' => Mage::helper('caship')->__('Title'),
            'required' => true,
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('caship')->__('It appears only in admin interface') . '</small></p>',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('caship')->__('Method name'),
            'title' => Mage::helper('caship')->__('Method name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('caship')->__('Method description'),
            'title' => Mage::helper('caship')->__('Method description'),
            'required' => true,
        ));

        $directions = $fieldset->addField('direction_type', 'select', array(
            'name' => 'direction_type',
            'label' => Mage::helper('caship')->__('Directions'),
            'title' => Mage::helper('caship')->__('Directions'),
            'values' => array(
                array('label' => '3 way', 'value' => 3),
                array('label' => '2 way', 'value' => 2),
                array('label' => '1 way', 'value' => 1),
            )
        ));

        $isDinamic = $fieldset->addField('dinamic_price', 'select', array(
            'name' => 'dinamic_price',
            'label' => Mage::helper('caship')->__('Price Source'),
            'title' => Mage::helper('caship')->__('Price Source'),
            'values' => Mage::getModel('caship/config_dinamicPrice')->toOptionArray(),
        ));

        $price = $fieldset->addField('price', 'text', array(
            'name' => 'price',
            'label' => Mage::helper('caship')->__('Price'),
            'title' => Mage::helper('caship')->__('Price'),
        ));

        $fieldset->addField('company_type', 'select', array(
            'name' => 'company_type',
            'label' => Mage::helper('caship')->__('Carrier / Module'),
            'title' => Mage::helper('caship')->__('Carrier / Module'),
            'values' => Mage::getModel('caship/config_shippingCompanyAll')->toOptionArray(false),
            /*'value' => $params['company_type']*/
        ));

        $carrierTo = $fieldset->addField('upsmethod_id', 'select', array(
            'name' => 'upsmethod_id',
            'label' => Mage::helper('caship')->__('Shipping Service of a Carrier (to)'),
            'title' => Mage::helper('caship')->__('Shipping Service of a Carrier (to)'),
            'required' => false,
            'values' => Mage::getModel('caship/config_upsmethod')->toOptionArray(),
        ));

        $carrierFrom = $fieldset->addField('upsmethod_id_2', 'select', array(
            'name' => 'upsmethod_id_2',
            'label' => Mage::helper('caship')->__('Shipping Service of a Carrier (from)'),
            'title' => Mage::helper('caship')->__('Shipping Service of a Carrier (from)'),
            'required' => true,
            'values' => Mage::getModel('caship/config_upsmethod')->toOptionArray(),
        ));

        $carrierTo2 = $fieldset->addField('upsmethod_id_3', 'select', array(
            'name' => 'upsmethod_id_3',
            'label' => Mage::helper('caship')->__('Shipping Service of a Carrier (to 2)'),
            'title' => Mage::helper('caship')->__('Shipping Service of a Carrier (to 2)'),
            'required' => true,
            'values' => Mage::getModel('caship/config_upsmethod')->toOptionArray(),
        ));


        $addedValueType = $fieldset->addField('added_value_type', 'select', array(
            'name' => 'added_value_type',
            'label' => Mage::helper('caship')->__('Add extra price type'),
            'title' => Mage::helper('caship')->__('Add extra price type'),
            'values' => Mage::getModel('caship/config_addedValueType')->toOptionArray(),
        ));

        $addedValue = $fieldset->addField('added_value', 'text', array(
            'name' => 'added_value',
            'label' => Mage::helper('caship')->__('Add extra value'),
            'title' => Mage::helper('caship')->__('Add extra value'),
        ));

        $negotiated = $fieldset->addField('negotiated', 'select', array(
            'name' => 'negotiated',
            'label' => Mage::helper('caship')->__('Negotiated rates'),
            'title' => Mage::helper('caship')->__('Negotiated rates'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $negotiatedAmountFrom = $fieldset->addField('negotiated_amount_from', 'text', array(
            'name' => 'negotiated_amount_from',
            'label' => Mage::helper('caship')->__('Add price from which the Negotiated rates starts'),
            'title' => Mage::helper('caship')->__('Add price from which the Negotiated rates starts'),
            'value' => 0,
        ));

        /*$timeintransit = $fieldset->addField('timeintransit', 'select', array(
            'name' => 'timeintransit',
            'label' => Mage::helper('caship')->__('Time in transit'),
            'title' => Mage::helper('caship')->__('Time in transit'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $titshowformat = $fieldset->addField('tit_show_format', 'select', array(
            'name' => 'tit_show_format',
            'label' => Mage::helper('caship')->__('Time in transit format'),
            'title' => Mage::helper('caship')->__('Time in transit format'),
            'options' => array('days' => '1 day(s)', 'datetime' => '09 July 2016 10:30')
        ));

        $addday = $fieldset->addField('addday', 'text', array(
            'name' => 'addday',
            'label' => Mage::helper('caship')->__('Additional days'),
            'title' => Mage::helper('caship')->__('Additional days'),
            'value' => 0,
        ));*/


        /*$increment_price_by_weight = $fieldset->addField('increment_price_by_weight', 'text', array(
            'name' => 'increment_price_by_weight',
            'label' => Mage::helper('caship')->__('Weight jump for price doubling'),
            'title' => Mage::helper('caship')->__('Weight jump for price doubling'),
        ));

        $increment_package_by_weight = $fieldset->addField('increment_package_by_weight', 'text', array(
            'name' => 'increment_package_by_weight',
            'label' => Mage::helper('caship')->__('Weight jump for new package'),
            'title' => Mage::helper('caship')->__('Weight jump for new package'),
        ));*/

        $fieldset->addField('amount_min', 'text', array(
            'name' => 'amount_min',
            'label' => Mage::helper('caship')->__('Minimum Order Amount'),
            'title' => Mage::helper('caship')->__('Minimum Order Amount'),
            'value' => 0,
        ));
        $fieldset->addField('amount_max', 'text', array(
            'name' => 'amount_max',
            'label' => Mage::helper('caship')->__('Maximum Order Amount'),
            'title' => Mage::helper('caship')->__('Maximum Order Amount'),
            'value' => 0,
            'after_element_html' => '<p class="note"><span>' . Mage::helper('caship')->__('If 0 then infinity') . '</span></p>',
        ));
        $fieldset->addField('weight_min', 'text', array(
            'name' => 'weight_min',
            'label' => Mage::helper('caship')->__('Minimum Order Weight'),
            'title' => Mage::helper('caship')->__('Minimum Order Weight'),
            'value' => 0,
        ));
        $fieldset->addField('weight_max', 'text', array(
            'name' => 'weight_max',
            'label' => Mage::helper('caship')->__('Maximum Order Weight'),
            'title' => Mage::helper('caship')->__('Maximum Order Weight'),
            'value' => 0,
            'after_element_html' => '<p class="note"><span>' . Mage::helper('caship')->__('If 0 then infinity') . '</span></p>',
        ));
        $fieldset->addField('qty_min', 'text', array(
            'name' => 'qty_min',
            'label' => Mage::helper('caship')->__('Minimum Product Quantity'),
            'title' => Mage::helper('caship')->__('Minimum Product Quantity'),
            'value' => 0,
        ));
        $fieldset->addField('qty_max', 'text', array(
            'name' => 'qty_max',
            'label' => Mage::helper('caship')->__('Maximum Product Quantity'),
            'title' => Mage::helper('caship')->__('Maximum Product Quantity'),
            'value' => 0,
            'after_element_html' => '<p class="note"><span>' . Mage::helper('caship')->__('If 0 then infinity') . '</span></p>',
        ));
        $fieldset->addField('zip_min', 'text', array(
            'name' => 'zip_min',
            'label' => Mage::helper('caship')->__('Minimum ZIP/Postal code'),
            'title' => Mage::helper('caship')->__('Minimum ZIP/Postal code'),
            'value' => "",
        ));

        $fieldset->addField('zip_max', 'text', array(
            'name' => 'zip_max',
            'label' => Mage::helper('caship')->__('Maximum ZIP/Postal code'),
            'title' => Mage::helper('caship')->__('Maximum ZIP/Postal code'),
            'value' => "",
        ));
        $tax = $fieldset->addField('tax', 'select', array(
            'name' => 'tax',
            'label' => Mage::helper('caship')->__('Tax'),
            'title' => Mage::helper('caship')->__('Tax'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        /*$fieldset->addField('showifnot', 'select', array(
            'name' => 'showifnot',
            'label' => Mage::helper('caship')->__('Show Method if Not Applicable'),
            'title' => Mage::helper('caship')->__('Show Method if Not Applicable'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));*/

        $is_country_all = $fieldset->addField('is_country_all', 'select', array(
            'name' => 'is_country_all',
            'label' => __('Ship to Applicable Countries'),
            'title' => __('Ship to Applicable Countries'),
            'values' => Mage::getModel('adminhtml/system_config_source_shipping_allspecificcountries')->toOptionArray(),
        ));

        $country_ids = $fieldset->addField('country_ids', 'multiselect', array(
            'name' => 'country_ids',
            'label' => Mage::helper('caship')->__('Allowed Countries'),
            'title' => Mage::helper('caship')->__('Allowed Countries'),
            'required' => true,
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
        ));

        $fieldset->addField('user_group_ids', 'multiselect', array(
            'name' => 'user_group_ids',
            'label' => Mage::helper('caship')->__('User Groups'),
            'title' => Mage::helper('caship')->__('User Groups'),
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_customer_group_multiselect')->toOptionArray(),
        ));
        $fieldset->addField('product_categories', 'multiselect', array(
            'name' => 'product_categories',
            'label' => Mage::helper('caship')->__('Product of categories'),
            'title' => Mage::helper('caship')->__('Product of categories'),
            'required' => false,
            'values' => Mage::getModel('caship/config_productCategories')->toOptionArray(false),
        ));

        

        $fieldset->addField('sort', 'text', array(
            'name' => 'sort',
            'label' => Mage::helper('caship')->__('Sort'),
            'title' => Mage::helper('caship')->__('Sort'),
            'required' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => Mage::helper('caship')->__('Enabled'),
            'title' => Mage::helper('caship')->__('Enabled'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($price->getHtmlId(), $price->getName())
            ->addFieldMap($isDinamic->getHtmlId(), $isDinamic->getName())
            ->addFieldMap($negotiated->getHtmlId(), $negotiated->getName())
            ->addFieldMap($negotiatedAmountFrom->getHtmlId(), $negotiatedAmountFrom->getName())
            ->addFieldMap($directions->getHtmlId(), $directions->getName())
            ->addFieldMap($carrierTo->getHtmlId(), $carrierTo->getName())
            ->addFieldMap($carrierFrom->getHtmlId(), $carrierFrom->getName())
            /*->addFieldMap($timeintransit->getHtmlId(), $timeintransit->getName())
            ->addFieldMap($addday->getHtmlId(), $addday->getName())*/
            ->addFieldMap($is_country_all->getHtmlId(), $is_country_all->getName())
            ->addFieldMap($country_ids->getHtmlId(), $country_ids->getName())
            ->addFieldMap($tax->getHtmlId(), $tax->getName())
            /*->addFieldMap($titshowformat->getHtmlId(), $titshowformat->getName())*/
            ->addFieldMap($addedValueType->getHtmlId(), $addedValueType->getName())
            ->addFieldMap($addedValue->getHtmlId(), $addedValue->getName())
            /*->addFieldMap($increment_price_by_weight->getHtmlId(), $increment_price_by_weight->getName())
            ->addFieldMap($increment_package_by_weight->getHtmlId(), $increment_package_by_weight->getName())*/
            ->addFieldDependence($price->getName(), $isDinamic->getName(), 0)
            ->addFieldDependence($negotiated->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($negotiatedAmountFrom->getName(), $negotiated->getName(), 1)
            ->addFieldDependence($carrierTo->getName(), $directions->getName(), 3)
            ->addFieldDependence($carrierFrom->getName(), $directions->getName(), array('2','3'))
            /*->addFieldDependence($timeintransit->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($addday->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($addday->getName(), $timeintransit->getName(), 1)
            ->addFieldDependence($addday->getName(), $titshowformat->getName(), 'days')
            ->addFieldDependence($titshowformat->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($titshowformat->getName(), $timeintransit->getName(), 1)*/
            ->addFieldDependence($country_ids->getName(), $is_country_all->getName(), 1)
            ->addFieldDependence($tax->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($addedValueType->getName(), $isDinamic->getName(), 1)
            ->addFieldDependence($addedValue->getName(), $isDinamic->getName(), 1)
            /*->addFieldDependence($increment_price_by_weight->getName(), $isDinamic->getName(), 0)
            ->addFieldDependence($increment_package_by_weight->getName(), $isDinamic->getName(), 0)*/
        );
        if (Mage::getSingleton('adminhtml/session')->getAccountData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getAccountData());
            Mage::getSingleton('adminhtml/session')->setAccountData(null);
        } elseif (Mage::registry('method_data') && count(Mage::registry('method_data')->getData()) > 0) {
            $data = Mage::registry('method_data')->getData();
            $form->setValues($data);
        }
        return parent::_prepareForm();
    }
}