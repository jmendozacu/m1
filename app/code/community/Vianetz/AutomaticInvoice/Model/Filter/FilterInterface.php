<?php
/**
 * AutomaticInvoice Filter Interface Class
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
interface Vianetz_AutomaticInvoice_Model_Filter_FilterInterface
{
    /**
     * Determines whether the current filter does match the order or not.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $sourceType
     *
     * @return boolean
     */
    public function isMatch(Mage_Sales_Model_Order $order, $sourceType);
}