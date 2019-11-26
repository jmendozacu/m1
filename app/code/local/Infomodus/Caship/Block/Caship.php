<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Caship_Block_Caship extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCaship()
     { 
        if (!$this->hasData('caship')) {
            $this->setData('caship', Mage::registry('caship'));
        }
        return $this->getData('caship');
        
    }
}