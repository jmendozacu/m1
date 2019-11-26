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
 * Abstract Resource Model
 */
class Magpleasure_Common_Model_Resource_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    const LINK_DATA_KEY = 'data_key';
    const LINK_DB_POSTFIX = 'db_postfix';
    const LINK_DB_DATA_KEY = 'db_data_key';

    protected $_useStoreLabels = false;
    protected $_useUpdateDatetimeHelper = false;

    protected $_links = array();

    protected function _construct(){}

    /**
     * Perform actions after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);

        # Load Store Labels
        if ($this->getUseStoreLabels()){
            $labels = $this->_loadLabels($object->getId());
            foreach ($labels as $storeId => $label){
                $object->setData("store_label_".$storeId, $label);
            }
        }

        # Load Linked Data
        if (count($this->_links)){
            foreach ($this->_links as $key=>$params){
                $this->_loadAbstractLink($object, @$params[self::LINK_DATA_KEY], @$params[self::LINK_DB_POSTFIX], @$params[self::LINK_DB_DATA_KEY]);
            }
        }

        return $this;
    }

    /**
     * Get Use Store Labels
     *
     * @return bool
     */
    public function getUseStoreLabels()
    {
        return $this->_useStoreLabels;
    }

    /**
     * Set Use Store Labels
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function setUseStoreLabels($value)
    {
        $this->_useStoreLabels = $value;
        return $this;
    }

    /**
     * Set Use Update DateTime Helper
     *
     * @param $value
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function setUseUpdateDatetimeHelper($value)
    {
        $this->_useUpdateDatetimeHelper = $value;
        return $this;
    }

    /**
     * Get Use Update DateTime Helper
     *
     * @return bool
     */
    public function getUseUpdateDatetimeFilter()
    {
        return $this->_useUpdateDatetimeHelper;
    }


    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        # Save Store Labels
        if ($this->getUseStoreLabels()){
            $this->_saveLabels($object->getId(), $object->getStoreLabels());
        }

        # Save Linked Data
        if (count($this->_links)){
            foreach ($this->_links as $key=>$params){
                $this->_saveAbstractLink($object, @$params[self::LINK_DATA_KEY], @$params[self::LINK_DB_POSTFIX], @$params[self::LINK_DB_DATA_KEY]);
            }
        }

        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract|Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        # Update Datetime Proceed
        if ($this->getUseUpdateDatetimeFilter()){
            $now = new Zend_Date();
            $now = $now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            if (!$object->getId()){
                $object->setCreatedAt($now);
            }
            $object->setUpdatedAt($now);
        }
        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Save Labels to Table <main_table>_label
     *
     * @param int $entityId
     * @param array $labels
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _saveLabels($entityId, array $labels = null)
    {
        if (!$labels || !is_array($labels)){
            return $this;
        }

        $labelTable = $this->getMainTable()."_label";
        $keyField = $this->getIdFieldName();

        # Delete Old Labels
        $deleted = $this
            ->_getWriteAdapter()
            ->beginTransaction()
            ->delete($labelTable, "{$keyField} = '{$entityId}'")
        ;

        # Insert new ones
        $data = array();
        foreach ($labels as $storeId => $label) {
            if (empty($label)) {
                continue;
            }
            $data[] = array(
                $keyField   => $entityId,
                'store_id'  => $storeId,
                'label'     => $label
            );
        }

        try {
            if (!empty($data)) {
                foreach ($data as $row){
                    $this->_getWriteAdapter()->insert($labelTable, $row);
                }
            }
        } catch (Exception $e){
            $this
                ->_commonHelper()
                ->getException()
                ->logException($e)
            ;
        }

        $this->_getWriteAdapter()->commit();

        return $this;
    }

    /**
     * Load Labels from Table <main_table>_label
     *
     * @param int $entityId
     * @return array
     */
    protected function _loadLabels($entityId)
    {
        $labelsData = array();

        $labelTable = $this->getMainTable()."_label";
        $keyField = $this->getIdFieldName();

        $select = $this->_getReadAdapter()->select();
        $select
            ->from($labelTable, array('store_id', 'label'))
            ->where("{$keyField} = ?", $entityId)
            ;

        try {
            foreach ($this->_getReadAdapter()->fetchAll($select) as $row){
                $labelsData[$row['store_id']] = $row['label'];
            }
        } catch (Exception $e){
            $this
                ->_commonHelper()
                ->getException()
                ->logException($e)
            ;
        }

        return $labelsData;
    }

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Add Abstract Data Link
     *
     * @param $name
     * @param $dataKey
     * @param $dbPostfix
     * @param $dbDataKey
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _addDataLink($name, $dataKey, $dbPostfix, $dbDataKey)
    {
        $this->_links[$name] = array(
            self::LINK_DATA_KEY => $dataKey,
            self::LINK_DB_POSTFIX  => $dbPostfix,
            self::LINK_DB_DATA_KEY => $dbDataKey,
        );
        return $this;
    }


    /**
     * Remove Abstract Data Link
     *
     * @param $name
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _removeDataLink($name)
    {
        if (isset($this->_links[$name])){
            unset($this->_links[$name]);
        }
        return $this;
    }

    /**
     * Save Linked Abstract Data
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $dataKey
     * @param string $dbPostfix
     * @param string $dbDataKey
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _saveAbstractLink(Mage_Core_Model_Abstract $object, $dataKey, $dbPostfix, $dbDataKey)
    {
        if (is_array($object->getData($dataKey))){
            $storeTable = $this->getMainTable().$dbPostfix;
            $write = $this->_getWriteAdapter();
            $write->beginTransaction();
            $write->delete($storeTable, "`{$this->getIdFieldName()}` = '{$object->getId()}'");
            if (is_array($object->getData($dataKey))){
                foreach ($object->getData($dataKey) as $linkValue){
                    $write->insert($storeTable, array(
                        $this->getIdFieldName() => $object->getId(),
                        $this->_getLocalDataKey($dbDataKey) => $linkValue,
                    ));
                }
            }
            $write->commit();
        }
        return $this;
    }

    protected function _getLocalDataKey($value)
    {
        if (is_array($value)){
            foreach ($value as $local=>$remote){
                return $local;
            }
        } else {
            return $value;
        }
    }

    protected function _getRemoteDataKey($value)
    {
        if (is_array($value)){
            foreach ($value as $local=>$remote){
                return $remote;
            }
        } else {
            return $value;
        }
    }

    /**
     * Load Linked Abstract Data
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $dataKey
     * @param string $dbPostfix
     * @param string $dbDataKey
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    protected function _loadAbstractLink(Mage_Core_Model_Abstract $object, $dataKey, $dbPostfix, $dbDataKey)
    {
        if ($object->getData($this->getIdFieldName())){
            $storeTable = $this->getMainTable().$dbPostfix;
            $read = $this->_getReadAdapter();
            $select = new Zend_Db_Select($read);
            $select
                ->from($storeTable, array($this->_getLocalDataKey($dbDataKey)))
                ->where($this->getIdFieldName()." = ?", $object->getId());
            ;

            $result = array();
            foreach ($read->fetchAll($select) as $row){
                $result[] = $row[$this->_getLocalDataKey($dbDataKey)];
            }
            $object->setData($dataKey, $result);
            $object->setOrigData($dataKey, $result);
        }

        return $this;
    }

    /**
     * Write Adapter
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getWriteAdapter()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * Get Read Adapter
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getReadAdapter()
    {
        return $this->_getReadAdapter();
    }

    /**
     * Delete all linked records
     *
     * @param $fieldName
     * @param $fieldValue
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function deleteRowsByLink($fieldName, $fieldValue)
    {
        $mainTable = $this->getMainTable();
        if ($mainTable){
            $write = $this->getWriteAdapter();
            $write
                ->beginTransaction()
                ->delete($mainTable, "`{$fieldName}` = '{$fieldValue}'")
                ;
            $write->commit();
        }
        return $this;
    }

    /**
     * Load Absctract Collection by few key fields
     *
     * @param Mage_Core_Model_Abstract $object
     * @param array $data
     * @return Magpleasure_Common_Model_Resource_Abstract
     */
    public function loadByFewFields(Mage_Core_Model_Abstract $object, array $data)
    {
        /** @var $collection Mage_Core_Model_Resource_Db_Collection_Abstract */
        $collection = $object->getCollection();
        if ($collection){
            foreach ($data as $field=>$value){
                $collection->addFieldToFilter($field, $value);
            }

            foreach ($collection as $item){
                /** @var $item Mage_Core_Model_Abstract */
                if ($itemId = $item->getId()){
                    $itemModel = Mage::getModel($object->getResourceName())->load($itemId);
                    $object->setData($itemModel->getData());
                }
            }
        }

        return $this;
    }
}