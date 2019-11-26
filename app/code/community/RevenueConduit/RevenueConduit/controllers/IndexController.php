<?php
/**
 * RevenueConduit
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA available
 * through the world-wide-web at this URL:
 * http://revenueconduit.com/magento/license
 *
 * MAGENTO EDITION USAGE NOTICE
 *
 * This package is designed for Magento COMMUNITY edition.
 * =================================================================
 *
 * @package    RevenueConduit
 * @copyright  Copyright (c) 2012-2013 RevenueConduit. (http://www.revenueconduit.com)
 * @license    http://revenueconduit.com/magento/license
 * @terms      http://revenueconduit.com/magento/terms
 * @author     Parag Jagdale
 */
?>

<?php
class RevenueConduit_RevenueConduit_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

    }

    public function abandonedAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $savedQuote = Mage::getModel('sales/quote')
            ->load($this->getRequest()->getParam('quoteid'));
        if ($quote->getId() != $savedQuote->getId() && $savedQuote->getId()) {
            $quote->merge($savedQuote)->save();
	$cart = Mage::getModel('checkout/cart')
                ->setQuote($quote)
                ->init()
                ->save();

		$old_quote_id = $this->getRequest()->getParam('quoteid');
		if(!empty($old_quote_id)){
			Mage::getSingleton('core/session')->setOriginalQuoteId($old_quote_id);
		}

                //Delete the previous quote id using checkout delete webhook
			//Commeted this as we dont need delete webhook for previous quote id. We are now sending checkout create with new quote id as well as old quote id. So we dont need the following line
                //Mage::getModel('revenueconduit/observer')->SendRequest("checkouts/delete", 0, 0, 0, 0, $this->getRequest()->getParam('quoteid'));
        }
        $this->_redirect('checkout/cart');
    }
}
