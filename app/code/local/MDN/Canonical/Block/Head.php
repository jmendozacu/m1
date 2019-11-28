<?php
class MDN_Canonical_Block_Head extends Mage_Core_Block_Template {
	function getCanonicalUrl(){
		// get current url
		$url = mage::helper('core/url')->getCurrentUrl();

		// parse url
		$parsedUrl = parse_url($url);

		$scheme = $parsedUrl['scheme'];
		$host = $parsedUrl['host'];
		$path = $parsedUrl['path'];
		$path = preg_replace('#/$#',"", $path);

		// optional parameters
		$query = isset($parsedUrl['query']) ? $parsedUrl['query'] : null ;

		// build canonical url
		$canonicalUrl = $scheme.'://'.$host.$path;

		// get parameters
		$q = $this->getRequest()->getParam('q');
		$p = $this->getRequest()->getParam('p');

		// add parameters
		if($p != "" && $q != "")
			$canonicalUrl .= '?p='.$p.'&q='.$q;
		elseif($p != "" && $q == "")
			$canonicalUrl .= '?p='.$p;
		elseif($p == "" && $q != "")
			$canonicalUrl .= '?q='.$q;

		return $canonicalUrl;
	}
}
