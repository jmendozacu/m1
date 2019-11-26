<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Promo_CartController extends Mage_Core_Controller_Front_Action
{
    public function updateAction()
    {
        $productId = $this->getRequest()->getParam('product_id');

        $product = Mage::getModel('catalog/product')->load($productId);

        if ($product->getId())
        {
            $limits  = Mage::getSingleton('ampromo/registry')->getLimits();

            $sku = $product->getSku();

            $addAllRule = isset($limits[$sku]) && $limits[$sku] > 0;
            $addOneRule = false;
            if (!$addAllRule)
            {
                foreach ($limits['_groups'] as $ruleId => $rule)
                {
                    if (in_array($sku, $rule['sku']))
                    {
                        $addOneRule = $ruleId;
                    }
                }

            }
            if ($addAllRule || $addOneRule)
            {
                $super = $this->getRequest()->getParam('super_attributes');
                $options = $this->getRequest()->getParam('options');
                $bundleOptions = $this->getRequest()->getParam('bundle_option');

                Mage::helper('ampromo')->addProduct($product, $super, $options, $bundleOptions, $addOneRule);
            }
        }

        if ($returnUrl = $this->_getRefererUrl())
        {
            $this->getResponse()->setRedirect($returnUrl);
        }
    }
}
