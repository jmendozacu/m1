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
class Vianetz_AutomaticInvoice_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    /** @var Vianetz_AutomaticInvoice_Helper_Data */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = Mage::helper('automaticinvoice');
    }

    public function testInvoicePrefixWithDirSeparatorCharCorrectlySanitized()
    {
        $this->assertEquals('16_20130402', $this->_helper->stringToFilename('16/20130402'));
    }

    public function testInvoicePrefixWithSpecialCharsCorrectlySanitized()
    {
        $this->assertEquals('1__$_001_', $this->_helper->stringToFilename('1:_$&001#'));
    }
}