<?php
/**
 * GoMage LightCheckout Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2018 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 5.9.4
 * @since        Class available since Release 1.0
 */

class GoMage_Checkout_Block_Onepage_Review extends GoMage_Checkout_Block_Onepage_Abstract {
	
	function getCustomerComment() {
		
		return strval($this->getCheckout()->getQuote()->getGomageCheckoutCustomerComment());
	}

}