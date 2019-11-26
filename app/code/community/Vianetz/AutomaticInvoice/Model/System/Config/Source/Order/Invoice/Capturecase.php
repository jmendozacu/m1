<?php
/**
 * AutomaticInvoice Capture Model Source Model
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
 * @copyright   Copyright (c) 2006-13 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
class Vianetz_AutomaticInvoice_Model_System_Config_Source_Order_Invoice_Capturecase
{
    /**
     * Return available cature modes for dropdown.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $modeArray = array(
            array(
                'label' => 'Capture Online',
                'value' => Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE
            ),
            array(
                'label' => 'Capture Offline',
                'value' => Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE
            ),
            array(
                'label' => 'Do Not Capture',
                'value' => Mage_Sales_Model_Order_Invoice::NOT_CAPTURE
            )
        );

        return $modeArray;
    }
}