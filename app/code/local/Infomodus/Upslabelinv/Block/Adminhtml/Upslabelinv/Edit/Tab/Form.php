<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('upslabelinv_form', array('legend'=>Mage::helper('upslabelinv')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('upslabelinv')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('upslabelinv')->__('Withdraw'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('upslabelinv')->__('No'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('upslabelinv')->__('Yes'),
              ),
          ),
      ));
     
      $fieldset->addField('order_id', 'text', array(
          'name'      => 'order_id',
          'label'     => Mage::helper('upslabelinv')->__('Order Id'),
          'title'     => Mage::helper('upslabelinv')->__('Order Id'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('labelname', 'text', array(
          'name'      => 'labelname',
          'label'     => Mage::helper('upslabelinv')->__('Label Name'),
          'title'     => Mage::helper('upslabelinv')->__('Label Name'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('shipmentidentificationnumber', 'text', array(
          'name'      => 'shipmentidentificationnumber',
          'label'     => Mage::helper('upslabelinv')->__('Shipment Identification Number'),
          'title'     => Mage::helper('upslabelinv')->__('Shipment Identification Number'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('trackingnumber', 'text', array(
          'name'      => 'trackingnumber',
          'label'     => Mage::helper('upslabel')->__('Tracking Number'),
          'title'     => Mage::helper('upslabel')->__('Tracking Number'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('shipmentdigest', 'text', array(
          'name'      => 'shipmentdigest',
          'label'     => Mage::helper('upslabelinv')->__('Shipment Digest'),
          'title'     => Mage::helper('upslabelinv')->__('Shipment Digest'),
          'required'  => true,
          'readonly' => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getUpslabelData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getUpslabelData());
          Mage::getSingleton('adminhtml/session')->setUpslabelData(null);
      } elseif ( Mage::registry('upslabelinv_data') ) {
          $form->setValues(Mage::registry('upslabelinv_data')->getData());
      }
      return parent::_prepareForm();
  }
}