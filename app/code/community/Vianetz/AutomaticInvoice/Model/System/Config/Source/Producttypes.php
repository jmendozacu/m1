<?php
/**
 * VIANETZ
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
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
class Vianetz_AutomaticInvoice_Model_System_Config_Source_Producttypes extends Mage_Catalog_Model_Product_Type
{
    /**
     * @return array
     */
    static public function toOptionArray()
    {
        $options = array();
        $i = 0;
        foreach (self::getTypes() as $typeId => $type) {
            $options[$i]['label'] = Mage::helper('catalog')->__($type['label']);
            $options[$i]['value'] = $typeId;
            $i++;
        }

        return $options;
    }
}