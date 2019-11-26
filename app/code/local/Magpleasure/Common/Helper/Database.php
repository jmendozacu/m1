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
class Magpleasure_Common_Helper_Database extends Mage_Core_Helper_Abstract
{
    protected $_tableNames;

    protected $_tableNameFixes = array(
        'customer/customer' => 'customer/entity',
    );

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Get Wrapped Table Name
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        if (!isset($this->_tableNames[$tableName])) {
            /** @var $resource Mage_Core_Model_Resource */
            $resource = Mage::getSingleton('core/resource');
            $tableName = str_replace(array_keys($this->_tableNameFixes), array_values($this->_tableNameFixes), $tableName);
            $this->_tableNames[$tableName] = $resource->getTableName($tableName);
        }
        return $this->_tableNames[$tableName];
    }

    public function getWriteConnection()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('core_write');
    }

    public function getReadConnection()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('core_read');
    }

    public function getFields($tableName)
    {
        $read = $this->getReadConnection();
        return $this->_commonHelper()->getArrays()->rowsKeysStrToLower(
                $read->fetchAll("SHOW COLUMNS FROM `{$tableName}`;")
        );
    }
}