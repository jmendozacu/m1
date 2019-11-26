<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabelinv_Block_Upslabelinv extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getUpslabel()     
     { 
        if (!$this->hasData('upslabelinv')) {
            $this->setData('upslabelinv', Mage::registry('upslabelinv'));
        }
        return $this->getData('upslabelinv');
        
    }
}