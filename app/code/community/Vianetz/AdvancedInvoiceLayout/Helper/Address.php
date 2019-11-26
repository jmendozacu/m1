<?php
/**
 * AdvancedInvoiceLayout address helper class
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
class Vianetz_AdvancedInvoiceLayout_Helper_Address extends Mage_Core_Helper_Abstract
{
    /**
     * Check if both given addresses are equal.
     *
     * @param object $addressOne The first address to compare.
     * @param object $addressTwo The second address to compare.
     *
     * @return boolean returns true whether addresses are equal, false otherwise
     */
    public function isAddressEqual($addressOne, $addressTwo)
    {
        if (empty($addressOne) === true || empty($addressTwo) === true) {
            return false;
        }

        if ($addressOne->getCompany() != $addressTwo->getCompany()) {
            return false;
        }
        if ($addressOne->getFirstname() != $addressTwo->getFirstname()) {
            return false;
        }
        if ($addressOne->getLastname() != $addressTwo->getLastname()) {
            return false;
        }
        if ($addressOne->getRegionId() != $addressTwo->getRegionId()) {
            return false;
        }
        if ($addressOne->getCountryId() != $addressTwo->getCountryId()) {
            return false;
        }
        if ($addressOne->getStreet() != $addressTwo->getStreet()) {
            return false;
        }
        if ($addressOne->getCity() != $addressTwo->getCity()) {
            return false;
        }

        return true;
    }
}
