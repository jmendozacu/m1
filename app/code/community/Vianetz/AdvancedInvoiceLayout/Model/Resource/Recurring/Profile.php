<?php
/**
 * AdvancedInvoiceLayout Recurring Profile Resource Model
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

class Vianetz_AdvancedInvoiceLayout_Model_Resource_Recurring_Profile extends Mage_Sales_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/recurring_profile_order', 'link_id');
    }

    /**
     * Return recurring profile id based on order id.
     *
     * @param integer $orderId
     *
     * @return null|integer
     */
    public function getProfileIdByOrderId($orderId)
    {
        $profileId = null;

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('order_id = ?', $orderId)
            ->limit(1);

        if ($data = $this->_getReadAdapter()->fetchRow($select)) {
            $profileId = $data['profile_id'];
        }

        return $profileId;
    }
}