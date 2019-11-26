<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Common
 * @version    0.5.7
 * @copyright  Copyright (c) 2012-2013 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

/**
 * Abstract Model
 */
class Magpleasure_Common_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Load Abstract Model by few key fields
     *
     * @param array $data
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function loadByFewFields(array $data)
    {
        $this->getResource()->loadByFewFields($this, $data);
        return $this;
    }

    /**
     * Retrieves Entity Label
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLabel($storeId = null)
    {
        if ($this->getResource()->getUseStoreLabels()){
            if (!$storeId){
                $storeId = Mage::app()->getStore()->getId();
            }

            return $this->getData('store_label_'.$storeId) ? $this->getData('store_label_'.$storeId) : $this->getData('label');
        } else {
            return parent::getLabel();
        }
    }

}