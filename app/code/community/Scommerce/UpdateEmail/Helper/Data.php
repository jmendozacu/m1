<?php
class Scommerce_UpdateEmail_Helper_Data extends Mage_Catalog_Helper_Data {
	function getEnable() {return Mage::getStoreConfig('scommerce_updateemail/general/enabled');}
}