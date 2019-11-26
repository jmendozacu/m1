<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Varieble Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class CommerceThemes_GuestToReg_Block_Adminhtml_Renderer_Paypalemailvalue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		#$orderId = $row->getData($this->getColumn()->getIndex());
		$orderId = $row->getData('entity_id');
		$resource = Mage::getSingleton('core/resource');
		$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
		$read = $resource->getConnection('core_read');
		#echo "TEST: " . $orderId;
		$select_qry3 = $read->query("SELECT * FROM `".$prefix."sales_flat_order_payment` WHERE parent_id = '".$orderId ."'");
		$order_payment_row = $select_qry3->fetch();
		#print_r($row->getData());
		#echo "TEST: " . $order_payment_row['additional_information'];
		$order_payment_unserializevalues = unserialize($order_payment_row['additional_information']);
		#print_r($order_payment_unserializevalues);
		$data = $order_payment_unserializevalues['paypal_payer_email'];

        return $data;
    }
}
