<?php

/**
 * VES_PdfPro_Block_Sales_Order_Info
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('upslabelinv/sales/order/info.phtml'); /* for version 1.5 or high */  /* /buttons */
    }
    public function getPrintUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
        }
        return $this->getUrl('sales/order/print', array('order_id' => $order->getId()));
    }

}
