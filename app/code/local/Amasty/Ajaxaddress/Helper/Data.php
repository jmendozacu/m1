<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ajaxaddress
*/
class Amasty_Ajaxaddress_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function replaceFormatWithInputs($address, $format, $type)
    {
        $expr = ',{{(depend|if) [a-z12]+}}[^{]*{{var ([a-z12]+)}}[^{]*{{(/depend|/if)}},';
        $format = preg_replace($expr, '{{var $2}}', $format);
        $fields = array('prefix', 'firstname', 'middlename', 'lastname', 'suffix', 'company', 'street1', 'street2', 'city', 'region', 'country', 'postcode', 'telephone', 'fax');
        $remove = array('<br/>', '<br />', ',');
        $format = str_replace($remove, '', $format);
        
        $format = '<table cellpadding="4" cellspacing="4" border="0">' . $format;
        foreach ($fields as $field)
        {
            if ('country' == $field)
            {
                $editUrl = Mage::getModel('adminhtml/url')->getUrl('amajaxaddress/adminhtml_address/edit', array('forcesave' => 1));
                if (Mage::getStoreConfig('web/secure/use_in_adminhtml')) {
                    $editUrl = str_replace(Mage::getStoreConfig('web/unsecure/base_url'), Mage::getStoreConfig('web/secure/base_url'), $editUrl);
                }
                $element = Mage::app()->getLayout()->createBlock('directory/data')->getCountryHtmlSelect($address->getCountryId(), 'amajaxaddress[country_id]', 'amajaxadress_field_country_id');
                $element = str_replace('<select', '<select style="width: 228px;"', $element);
                $element = str_replace('<select', '<select onchange="javascript: amcountryChange(\'' . $editUrl . '\', \'' . $type . '\');"', $element);
            } elseif ('region' == $field)
            {
                $collection = Mage::getModel('directory/region')->getResourceCollection()
                                    ->addCountryFilter($address->getCountryId())
                                    ->load();
                if ($collection->getSize() > 0)
                {
                    $options = $collection->toOptionArray();
                    $element = Mage::app()->getLayout()->createBlock('core/html_select')
                                    ->setName('amajaxaddress[region_id]')
                                    ->setId('amajaxadress_field_region_id')
                                    ->setClass('required-entry validate-state')
                                    ->setValue($address->getRegionId())
                                    ->setOptions($options)
                                    ->getHtml();
                    $element = str_replace('<select', '<select style="width: 228px;"', $element);
                } else 
                {
                    $element = '<input class="input-text" style="width: 220px;" type="text" id="amajaxadress_field_' . $field . '" name="amajaxaddress[' . $field . ']" value="{{var ' . $field . '}}" />';
                }
            } else 
            {
                $element = '<input class="input-text" style="width: 220px;" type="text" id="amajaxadress_field_' . $field . '" name="amajaxaddress[' . $field . ']" value="{{var ' . $field . '}}" />';
            }
            $replacement = '<tr><td style="color: #6F8992;">' . $this->__(ucwords($field)) . ':&nbsp;&nbsp;</td><td>' . $element . '</td>';
            $format      = str_replace("{{var $field}}", $replacement, $format);
        }
        $format = $format . '</table>';
        
        return $format;
    }
}