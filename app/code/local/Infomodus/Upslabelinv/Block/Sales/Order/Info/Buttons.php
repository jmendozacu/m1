<?php
class Infomodus_Upslabelinv_Block_Sales_Order_Info_Buttons extends Mage_Sales_Block_Order_Info /* for 1.5 or high Mage_Sales_Block_Order_Info_Buttons */
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('upslabelinv/sales/order/info.phtml'); /* for version 1.5 or high */  /* /buttons */
    }
}
