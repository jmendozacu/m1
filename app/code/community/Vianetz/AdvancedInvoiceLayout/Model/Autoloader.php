<?php
/**
 * AdvancedInvoiceLayout Autoloader
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

//require_once 'dompdf-advancedinvoicelayout' . DS . 'lib' . DS . 'html5lib' . DS . 'Parser.php';
//require_once 'dompdf-advancedinvoicelayout' . DS . 'lib' . DS . 'php-font-lib' . DS . 'src' . DS . 'FontLib' . DS . 'Autoloader.php';
//require_once 'dompdf-advancedinvoicelayout' . DS . 'lib' . DS . 'php-svg-lib' . DS . 'src' . DS . 'autoload.php';

//require_once 'dompdf-advancedinvoicelayout' . DS . 'src' . DS . 'Autoloader.php';

class Vianetz_AdvancedInvoiceLayout_Model_Autoloader
{
    /**
     * @see \Dompdf\Autoloader::register()
     *
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addAutoloader(Varien_Event_Observer $observer)
    {
    	//Mage::log(__METHOD__ . ' BEFORE');
		if (@class_exists('\Dompdf\Autoloader')) {
			spl_autoload_register(array('\Dompdf\Autoloader', 'autoload'), false, true);
		}
        //Mage::log(__METHOD__ . ' AFTER');

        return $this;
    }
}