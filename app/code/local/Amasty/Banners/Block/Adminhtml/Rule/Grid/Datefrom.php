<?php 
class Amasty_Banners_Block_Adminhtml_Rule_Grid_Datefrom extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row)
	{
		return Mage::getModel('core/date')->gmtDate(null, $row->getFromDate());
	}
}