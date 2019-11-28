<?php
// 2019-11-29 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
final class MDN_Canonical_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {
	/**
	 * 2019-11-29 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @override
	 * @see Mage_Catalog_Block_Product_List_Toolbar::getPagerUrl()
	 * @used-by Mage_Catalog_Block_Product_List_Toolbar::getLimitUrl()
	 * @used-by Mage_Catalog_Block_Product_List_Toolbar::getModeUrl()
	 * @used-by Mage_Catalog_Block_Product_List_Toolbar::getOrderUrl()
	 * @param array $p
	 * @return string
	 */
	function getPagerUrl($p = []) {
		if (isset($p['p']) && 1 == $p['p']) {
			$p['p'] = null;
		}
		return $this->getUrl('*/*/*', ['_current' => true, '_escape' => true, '_query' => $p, '_use_rewrite' => true]);
	}
}