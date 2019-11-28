<?php
class MDN_Canonical_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager {
	function getPagerUrl($params=array()) {
		if (isset($params['p']) && $params['p'] == 1)
			   $params['p'] = NULL;

		$urlParams = array();
		$urlParams['_current']  = true;
		$urlParams['_escape']   = true;
		$urlParams['_use_rewrite']   = true;
		$urlParams['_query']    = $params;
		if(is_object(Mage::registry('current_product'))){
			$url=Mage::registry('current_product')->getUrl();
			if(isset($_GET['limit'])){
				if(isset($urlParams['_query']['p'])){
					$url=$url.'?p='.$urlParams['_query']['p'].'&limit='.$_GET['limit'];
				}elseif(!isset($urlParams['_query']['p']) && isset($urlParams['_query']['limit'])){
					$url=$url.'?limit='.$urlParams['_query']['limit'];
				}
			}else{
				if(isset($urlParams['_query']['p']) && !isset($urlParams['_query']['limit'])){
					$url=$url.'?p='.$urlParams['_query']['p'];
				}elseif(!isset($urlParams['_query']['p']) && isset($urlParams['_query']['limit'])){
					$url=$url.'?limit='.$urlParams['_query']['limit'];
				}
			}

			return $url;
		}else{
			return $this->getUrl('*/*/*', $urlParams);
		}
	}

}
