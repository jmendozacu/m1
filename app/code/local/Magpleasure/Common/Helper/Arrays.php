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
class Magpleasure_Common_Helper_Arrays extends Mage_Core_Helper_Abstract
{
    /**
     * Label-Value array to Array with Params
     *
     * @param array $array
     * @return array
     */
    public function valueLabelToParams(array $array)
    {
        $result = array();
        foreach ($array as $row){
            if (isset($row['value']) && isset($row['label'])){
                $result[$row['value']] = $row['label'];
            }
        }
        return $result;
    }

    public function paramsToValueLabel(array $array)
    {
        $result = array();
        foreach ($array as $value=>$label){
            $result[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $result;
    }

    /**
     * Modify keys of rows within strtolower()
     *
     * @param array $input
     * @return array
     */
    public function rowsKeysStrToLower(array $input)
    {
        $output = array();

        foreach ($input as $row){
            if (is_array($row)){
                $newRow = array();
                foreach ($row as $key=>$value){
                    $newRow[strtolower($key)] = $value;
                }
                $output[] = $newRow;
            } else {
                Mage::throwException("Unsupported array format.");
            }
        }

        return $output;
    }
}