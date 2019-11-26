<?php

/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 19.06.15
 * Time: 0:21
 */
class Infomodus_Caship_Model_Config_Import extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']) {

            $uploadDir = $this->_getUploadDir();

            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $csvLines = file($file['tmp_name']);
                $delimiter = ",";
                if(strpos($csvLines[0], ';') !== FALSE) {
                    $delimiter = ";";
                }
                $head = str_getcsv($csvLines[0], $delimiter);
                unset($csvLines[0]);
                $items = Mage::getModel('caship/method')->getCollection();

                foreach ($csvLines AS $row) {
                    $model = Mage::getModel('caship/method');
                    $csv = str_getcsv($row, $delimiter);
                    $storeManager = Mage::getModel('core/store');
                    foreach ($csv AS $key => $col) {
                        $colData = trim($col);
                        switch (trim($head[$key])) {
                            case '﻿title':
                                $items->addFieldToFilter('title', $colData);
                                if (count($items) > 0) {
                                    $model = Mage::getModel('caship/method')->load($items->getFirstItem()->getId());
                                }
                                $model->setTitle($colData);
                                break;
                            case 'name':
                                $model->setName($colData);
                                /*$items->addFieldToFilter('name', $colData);*/
                                break;
                            case 'description':
                                $model->setDescription($colData);
                                /*$items->addFieldToFilter('name', $colData);*/
                                break;
                            case 'dynamic_price':
                                $model->setDinamicPrice($colData);
                                /*$items->addFieldToFilter('dinamic_price', $colData);*/
                                break;
                            case 'price':
                                $model->setPrice($colData);
                                /*$items->addFieldToFilter('price', $colData);*/
                                break;
                            case 'added_value_type':
                                $model->setAddedValueType($colData);
                                /*$items->addFieldToFilter('price', $colData);*/
                                break;
                            case 'added_value':
                                $model->setAddedValue($colData);
                                /*$items->addFieldToFilter('price', $colData);*/
                                break;
                            case 'carrier_code':
                                $model->setCompanyType(strtolower($colData));
                                /*$items->addFieldToFilter('company_type', strtolower($colData));*/
                                break;
                            case 'carrier_method_code':
                                $carrier_code = strtolower($csv[array_search('carrier_code', $head)]);
                                if($carrier_code == 'ups' || $carrier_code == 'upsinfomodus') {
                                    if (strlen($colData) == 1) {
                                        $colData = "0" . $colData;
                                    }
                                    $model->setUpsmethodId($colData);
                                    /*$items->addFieldToFilter('upsmethod_id', $colData);*/
                                }
                                break;
                            case 'carrier_method_code2':
                                $carrier_code = strtolower($csv[array_search('carrier_code', $head)]);
                                if($carrier_code == 'ups' || $carrier_code == 'upsinfomodus') {
                                    if (strlen($colData) == 1) {
                                        $colData = "0" . $colData;
                                    }
                                    $model->setUpsmethodId_2($colData);
                                    /*$items->addFieldToFilter('upsmethod_id', $colData);*/
                                }
                                break;
                            case 'carrier_method_code3':
                                $carrier_code = strtolower($csv[array_search('carrier_code', $head)]);
                                if($carrier_code == 'ups' || $carrier_code == 'upsinfomodus') {
                                    if (strlen($colData) == 1) {
                                        $colData = "0" . $colData;
                                    }
                                    $model->setUpsmethodId_3($colData);
                                    /*$items->addFieldToFilter('upsmethod_id', $colData);*/
                                }
                                break;
                            case 'store_code':
                                $store = $storeManager->load($colData);
                                $model->setStoreId($store->getId());
                                /*$items->addFieldToFilter('store_id', $store->getId());*/
                                break;
                            case 'country_ids':
                                if (strtolower($colData) != 'all') {
                                    $model->setCountryIds($colData);
                                    $items->addFieldToFilter('country_ids', $colData);
                                    $model->setIsCountryAll(1);
                                    /*$items->addFieldToFilter('is_country_all', 1);*/
                                } else {
                                    $model->setIsCountryAll(0);
                                    /*$items->addFieldToFilter('is_country_all', 0);*/
                                }
                                break;
                            case 'user_group_ids':
                                $model->setUserGroupIds($colData);
                                /*$items->addFieldToFilter('user_group_ids', $colData);*/
                                break;
                                case 'product_categories':
                                $model->setProductCategories($colData);
                                /*$items->addFieldToFilter('product_categories', $colData);*/
                                break;
                            case 'status':
                                $model->setStatus($colData);
                                /*$items->addFieldToFilter('status', $colData);*/
                                break;
                            case 'order_amount_min':
                                $model->setAmountMin(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('amount_min', str_replace(',', '.', $colData));*/
                                break;
                            case 'order_amount_max':
                                $model->setAmountMax(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('amount_max', str_replace(',', '.', $colData));*/
                                break;
                            case 'negotiated':
                                $model->setNegotiated($colData);
                                /*$items->addFieldToFilter('negotiated', $colData);*/
                                break;
                            case 'negotiated_amount_from':
                                $model->setNegotiatedFmountFrom(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('negotiated_amount_from', str_replace(',', '.', $colData));*/
                                break;
                            case 'tax':
                                $model->setTax($colData);
                                /*$items->addFieldToFilter('tax', $colData);*/
                                break;
                            case 'weight_min':
                                $model->setWeightMin(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('weight_min', str_replace(',', '.', $colData));*/
                                break;
                            case 'weight_max':
                                $model->setWeightMax(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('weight_max', str_replace(',', '.', $colData));*/
                                break;
                            case 'qty_min':
                                $model->setQtyMin(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('qty_min', str_replace(',', '.', $colData));*/
                                break;
                            case 'qty_max':
                                $model->setQtyMax(str_replace(',', '.', $colData));
                                /*$items->addFieldToFilter('qty_max', str_replace(',', '.', $colData));*/
                                break;
                            case 'zip_min':
                                $model->setZipMin($colData);
                                /*$items->addFieldToFilter('zip_min', $colData);*/
                                break;
                            case 'zip_max':
                                $model->setZipMax($colData);
                                /*$items->addFieldToFilter('zip_max', $colData);*/
                                break;
                            case 'time_in_transit':
                                $model->setTimeintransit($colData);
                                /*$items->addFieldToFilter('timeintransit', $colData);*/
                                break;
                            case 'add_day':
                                $model->setAddday($colData);
                                /*$items->addFieldToFilter('addday', $colData);*/
                                break;
                            case 'sort':
                                $model->setSort($colData);
                                /*$items->addFieldToFilter('addday', $colData);*/
                                break;
                            /*case 'weight_jump_for_price_doubling':
                                $model->setIncrementPriceByWeight($colData);
                                break;
                            case 'weight_jump_for_new_package':
                                $model->setIncrementPackageByWeight($colData);
                                break;*/
                        }
                    }
                    $model->save();
                }
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Mage_Core_Model_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);


            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                // Delete record before it is saved
                $this->delete();
                // Prevent record from being saved, since it was just deleted
                $this->_dataSaveAllowed = false;
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }

    protected function _getAllowedExtensions()
    {
        return array('csv');
    }
}