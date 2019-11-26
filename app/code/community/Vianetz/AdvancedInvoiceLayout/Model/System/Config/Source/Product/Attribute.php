<?php
/**
 * AdvancedInvoiceLayout Backend Attributes Source Model
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
class Vianetz_AdvancedInvoiceLayout_Model_System_Config_Source_Product_Attribute
{
    /**
     * Save options for caching.
     * @var array
     */
    protected $_options;

    /**
     * Return array of all relevant product attributes for display as multiselect in configuration backend.
     * @return array
     */
    public function toOptionArray()
    {
        // Feature is not supported prior to Magento version 1.5.0.0.
        if (version_compare(Mage::getVersion(), '1.5.0.0') <= 0) {
            return array();
        }

        if (empty($this->_options) === false) {
            return $this->_options;
        }

        $this->_options = array();
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->getItems();
        foreach ($attributes as $attribute) {
            if ($this->_isShowAttributeInBackend($attribute) === true) {
                $this->_options[] = array(
                    'label' => $attribute->getAttributeCode() . ' (' . $attribute->getFrontendLabel() . ')',
                    'value' => $attribute->getAttributeCode()
                );
            }
        }
        return $this->_options;
    }

    /**
     * Check if attribute should be shown in the multiselect list.
     * We have to exclude attributes such as gallery, system attributes, etc.
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function _isShowAttributeInBackend($attribute)
    {
        $allowedInputTypes = array('text', 'select', 'date', 'multiselect');

        return ($attribute->getIsUserDefined() == true) && (in_array($attribute->getFrontendInput(), $allowedInputTypes) === true);
    }
}
