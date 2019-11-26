<?php
/**
 * @copyright   Copyright (c) 2009-11 Amasty
 */
class Amasty_Promo_Model_Observer
{
    protected $_isHandled = array();
    protected $_toAdd = array();

    /**
     * Process sales rule form creation
     * @param   Varien_Event_Observer $observer
     */
    public function handleFormCreation($observer)
    {
        $actionsSelect = $observer->getForm()->getElement('simple_action');
        if ($actionsSelect){
            $vals = $actionsSelect->getValues();
            $vals[] = array(
                'value' => 'ampromo_items',
                'label' => Mage::helper('ampromo')->__('Auto add promo items with products'),
                
            );
            $vals[] = array(
                'value' => 'ampromo_cart',
                'label' => Mage::helper('ampromo')->__('Auto add promo items for the whole cart'),
                
            );
            $vals[] = array(
                'value' => 'ampromo_product',
                'label' => Mage::helper('ampromo')->__('Auto add the same product'),
                
            );
            
            $actionsSelect->setValues($vals);
            $actionsSelect->setOnchange('ampromo_hide()');
            
            $fldSet = $observer->getForm()->getElement('action_fieldset');
            $fldSet->addField('ampromo_type', 'select', array(
                    'name'     => 'ampromo_type',
                    'label' => Mage::helper('ampromo')->__('Type'),
                    'values' => array(
                        0 => Mage::helper('ampromo')->__('All SKUs below'),
                        1 => Mage::helper('ampromo')->__('One of the SKUs below')
                    ),
                ),
                'discount_amount'
            );
            $fldSet->addField('promo_sku', 'text', array(
                'name'     => 'promo_sku',
                'label' => Mage::helper('ampromo')->__('Promo Items'),
                'note'  => Mage::helper('ampromo')->__('Comma separated list of the SKUs'),
                ),
                'ampromo_type'
            );            
        }
        
        return $this; 
    }
    
    /**
     * Process quote item validation and discount calculation
     * @param   Varien_Event_Observer $observer
     */
    public function handleValidation($observer) 
    {
        $rule = $observer->getEvent()->getRule();

        if ($rule->getSimpleAction() == 'ampromo_product') {
            try {
            
                $item = $observer->getEvent()->getItem();

                $discountStep     = max(1, $rule->getDiscountStep());
                $maxDiscountQty = 100000;
                if ($rule->getDiscountQty()){
                    $maxDiscountQty   = intVal(max(1, $rule->getDiscountQty()));                    
                }
				
                $discountAmount   = max(1, $rule->getDiscountAmount());
                $qty = min(floor($item->getQty() / $discountStep) * $discountAmount, $maxDiscountQty);

                if ($item->getParentItemId())
                    return false;

                // we support only simple, configurable, virtual
                if (/*$item['product_type'] =='bundle' || */$item['product_type'] =='downloadable') {
                    return false;       
                } 

                if ($qty < 1){
                    return false;    
                }

                Mage::getSingleton('ampromo/registry')->addPromoItem($item->getProduct()->getData('sku'), $qty, $rule->getId());
            }
            catch (Exception $e){
                $hlp = Mage::helper('ampromo');
                $hlp->showMessage($hlp->__(
                    'We apologise, but there is an error while adding free items to the cart: %s', $e->getMessage()
                ));            
                return false;
            }   
        }
        
        if (!in_array($rule->getSimpleAction(), array('ampromo_items','ampromo_cart'))){
            return $this;
        }
       
        if (isset($this->_isHandled[$rule->getId()])){
            return $this;
        }
 
        $this->_isHandled[$rule->getId()] = true;
        
        $promoSku = $rule->getPromoSku();
        if (!$promoSku){
            return $this;     
        }  
        
        $quote = $observer->getEvent()->getQuote();
        
        $qty = $this->_getFreeItemsQty($rule, $quote);
        if (!$qty){
            //@todo  - add new field for label table
            // and show message like "Add 2 more products to get free items"
            return $this;         
        }

        if ($rule->getAmpromoType() == 1)
        {
            Mage::getSingleton('ampromo/registry')->addPromoItem(preg_split('/\s*,\s*/', $promoSku, -1, PREG_SPLIT_NO_EMPTY), $qty, $rule->getId());
        }
        else
        {
            $promoSku = explode(',', $promoSku);
            foreach ($promoSku as $sku){
                $sku = trim($sku);
                if (!$sku){
                    continue;
                }

                Mage::getSingleton('ampromo/registry')->addPromoItem($sku, $qty, $rule->getId());
            }
        }

        return $this;
    }

    public function onCollectTotalsBefore($observer)
    {
        Mage::getSingleton('ampromo/registry')->reset();
    }

    /**
     * Revert 'deleted' status and auto add all simple products without required options
     * @param $observer
     * @return $this
     */
    public function onAddressCollectTotalsAfter($observer)
    {
        $quote = $observer->getQuoteAddress()->getQuote();

        $items = $quote->getAllItems();

        if (Mage::getStoreConfigFlag('ampromo/general/auto_add'))
        {
            $toAdd  = Mage::getSingleton('ampromo/registry')->getPromoItems();
            unset($toAdd['_groups']);

            foreach ($items as $item)
            {
                $sku = $item->getProduct()->getData('sku');

                if (!isset($toAdd[$sku]))
                    continue;

                if ($item->getIsFree())
                    $toAdd[$sku]['qty'] -= $item->getQty();

                //unset($toAdd[$sku]); // to allow to decrease qty
            }

            $deleted = Mage::getSingleton('ampromo/registry')->getDeletedItems();

            $this->_toAdd = array();

            foreach ($toAdd as $sku => $item)
            {
                if ($item['auto_add'] && !isset($deleted[$sku]))
                {
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

                    for ($i = 0; $i < $item['qty']; $i++)
                        $this->_toAdd[] = $product;
                }
            }
        }
    }

    /**
     * Mark item as deleted to prevent it's auto-addition
     * @param $observer
     */
    public function onQuoteRemoveItem($observer)
    {
        if (Mage::app()->getRequest()->getActionName() != 'delete')
            return;

        $id = (int) Mage::app()->getRequest()->getParam('id');

        $item = $observer->getEvent()->getQuoteItem();

        if (($item->getId() == $id) && $item->getIsFree() && !$item->getParentId())
            Mage::getSingleton('ampromo/registry')->deleteProduct($item->getProduct()->getData('sku'));
    }

    public function decrementUsageAfterPlace($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        if (($order->getDiscountAmount() == 0) && (count($ruleIds) >= 1)) {
            foreach ($ruleIds as $ruleId) {
                if (!$ruleId) {
                    continue;
                }
                $rule = Mage::getModel('salesrule/rule');
                $rule->load($ruleId);
                if ($rule->getId()) {
                    $rule->setTimesUsed($rule->getTimesUsed() + 1);
                    $rule->save();

                    if ($customerId) {
                        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                        $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                        if ($ruleCustomer->getId()) {
                            $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + 1);
                        }
                        else {
                            $ruleCustomer
                            ->setCustomerId($customerId)
                            ->setRuleId($ruleId)
                            ->setTimesUsed(1);
                        }
                        $ruleCustomer->save();
                    }
                }
            }
            $coupon = Mage::getModel('salesrule/coupon');
            /** @var Mage_SalesRule_Model_Coupon */
            $coupon->load($order->getCouponCode(), 'code');
            if ($coupon->getId()) {
                $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
                $coupon->save();
                if ($customerId) {
                    $couponUsage = Mage::getResourceModel('salesrule/coupon_usage');
                    $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
                }
            }
        }        
    }
    
    // find qty
    // (for the whole cart it is $rule->getDiscountQty()
    // for items it is (qty * (number of matched non-free items) / step)
    protected function _getFreeItemsQty($rule, $quote)
    {  
        $amount = max(1, $rule->getDiscountAmount());
        $qty    = 0;
        if ('ampromo_cart' == $rule->getSimpleAction()){
            $qty = $amount;
        }
        else {
            $step = max(1, $rule->getDiscountStep());
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item) 
                    continue;
                    
                if ($item->getIsFree())
                    continue;
                    
                if (!$rule->getActions()->validate($item)) {
                    continue;
                }
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getProduct()->getParentProductId()) {
                    continue;
                }

                $qty = $qty + $item->getQty();
            } 
            
            $qty = floor($qty / $step) * $amount; 
            $max = $rule->getDiscountQty();
            if ($max){
                $qty = min($max, $qty);
            }
        }
        return $qty;        
    }  

    /**
     * Don't apply any discounts to free items
     * @param $observer
     */
    public function onProductAddAfter($observer)
    {
        $items = $observer->getItems();

        $this->_setItemPrefix($items);

        foreach ($items as $item)
        {
            if ($item->getIsFree())
                $item->setNoDiscount(true);
        }
    }

    /**
     * Remove all not allowed items
     * @param $observer
     */
    public function onCollectTotalsAfter($observer)
    {
        if (!Mage::getSingleton('checkout/session')->hasQuote())
            return;

        $allowedItems = Mage::getSingleton('ampromo/registry')->getPromoItems();
        $cart = Mage::getSingleton('checkout/cart');

        $customMessage = Mage::getStoreConfig('ampromo/general/message');

        foreach ($this->_toAdd as $product)
            Mage::helper('ampromo')->addProduct($product);

        $this->_toAdd = array();

        foreach ($observer->getQuote()->getAllItems() as $item)
        {
            if ($item->getIsFree())
            {
                if ($item->getParentItemId())
                    continue;

                $sku = $item->getProduct()->getData('sku');

                if (isset($allowedItems['_groups'][$item->getRuleId()])) // Add one of
                {
                    if ($allowedItems['_groups'][$item->getRuleId()]['qty'] <= 0)
                    {
                        $cart->removeItem($item->getId());
                    }
                    else if ($item->getQty() > $allowedItems['_groups'][$item->getRuleId()]['qty'])
                    {
                        $item->setQty($allowedItems['_groups'][$item->getRuleId()]['qty']);
                    }
                    if ($customMessage)
                        $item->setMessage($customMessage);

                    $allowedItems['_groups'][$item->getRuleId()]['qty'] -= $item->getQty();
                }
                else if (isset($allowedItems[$sku])) // Add all of
                {
                    if ($allowedItems[$sku]['qty'] <= 0)
                    {
                        $cart->removeItem($item->getId());
                    }
                    else if ($item->getQty() > $allowedItems[$sku]['qty'])
                    {
                        $item->setQty($allowedItems[$sku]['qty']);
                    }
                    if ($customMessage)
                        $item->setMessage($customMessage);

                    $allowedItems[$sku]['qty'] -= $item->getQty();
                }
                else
                    $cart->removeItem($item->getId());
            }
        }
    }

    public function onOrderPlaceBefore($observer)
    {
        $order = $observer->getOrder();

        $this->_setItemPrefix($order->getAllItems());
    }

    protected function _setItemPrefix($items)
    {
        if ($prefix = Mage::getStoreConfig('ampromo/general/prefix'))
        {
            foreach ($items as $item)
            {
                $buyRequest = $item->getBuyRequest();

                if (isset($buyRequest['options']['ampromo_rule_id']))
                {
                    $item->setName($prefix . ' ' . $item->getName());
                }
            }
        }
    }

    public function onCartItemUpdateBefore($observer)
    {
        $request = Mage::app()->getRequest();

        $id = (int)$request->getParam('id');
        $item = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($id);

        if ($item->getId() && $item->getIsFree())
        {
            $options = $request->getParam('options');
            $options['ampromo_rule_id'] = $item->getRuleId();
            $request->setParam('options', $options);
        }
    }
}
