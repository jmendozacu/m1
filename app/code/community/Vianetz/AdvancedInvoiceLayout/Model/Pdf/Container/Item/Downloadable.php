<?php
/**
 * AdvancedInvoiceLayout PDF crosssells model
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

/**
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Downloadable
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Downloadable extends Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Item_Default
{
    /**
     * Return Purchased links for order item (if downloadable product).
     *
     * @see Mage_Downloadable_Model_Sales_Order_Pdf_Items_Abstract::getLinks()
     *
     * @return array
     */
    public function getDownloadLinks()
    {
        $downloadLinksArray = array();
        if ($this->isDownloadable() === false) {
            return array();
        }

        $purchasedLinks = Mage::getModel('downloadable/link_purchased')
            ->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = Mage::getModel('downloadable/link_purchased_item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItem()->getId());
        $purchasedLinks->setPurchasedItems($purchasedItems);

        foreach ($purchasedLinks->getPurchasedItems() as $purchasedItem) {
            $linkData = $purchasedItem;
            $linkData->setLink($this->getUrl('downloadable/download/link', array('id' => $purchasedItem->getLinkHash(), '_secure' => true)));
            $downloadLinksArray[] = $linkData;
        }

        return $downloadLinksArray;
    }
}