<?php

class Infomodus_Upstablerates_Helper_Data extends Mage_Core_Helper_Abstract
{
	/** Check shipping method can use
		return method 1/2/3
	*/
	public function checkMethodAction(){
		$quote =	Mage::getSingleton('checkout/session')->getQuote();
		$quote =	Mage::getSingleton('checkout/session')->getQuote();
		$categoryAc	=	Mage::getStoreConfig('carriers/upstablerates/categoryac');
		$prc		=	1;
		$product_category_ids	=	array();
		foreach($quote->getAllVisibleItems() as $item){
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			if(!is_array($product->getCategoryIds())){
				$product_category_ids	= array_merge($product_category_ids,explode(",", $product->getCategoryIds()));
			}else{
				$product_category_ids	= array_merge($product_category_ids,$product->getCategoryIds());
			}
		}
		$product_category_ids	=	array_unique($product_category_ids);
		if(in_array($categoryAc,$product_category_ids)&&sizeof($product_category_ids)>1){
			$prc	=	3; // accessories + product 3
		}
		if(in_array($categoryAc,$product_category_ids)&&sizeof($product_category_ids)<=1){
			$prc	=	2; // only accessories 2
		}
		if(!in_array($categoryAc,$product_category_ids)){
			$prc	=	1; // product 1
		}
		return $prc;
	}
}