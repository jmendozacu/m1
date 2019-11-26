<?php

/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 27.02.15
 * Time: 15:26
 */
class Infomodus_Caship_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'caship';
    private $ratesDhl = NULL;
    private $ratesUps = NULL;

    public function collectRates(
        Mage_Shipping_Model_Rate_Request $request
    )
    {
        $storeId = 0;
        
        /* @var $result Mage_Shipping_Model_Rate_Result */
        $result = Mage::getModel('shipping/rate_result');
        $quote = Mage::helper('checkout')->getQuote()->getShippingAddress()->getData();
        if (isset($quote['total_qty'])) {
            $quantity = $quote['total_qty'];

            $orderAmount = $quote['base_subtotal_incl_tax'];
            if ($orderAmount <= 0) {
                $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
                $orderAmount = $totals["subtotal"]->getValue();
            }
            $weight = $request->getPackageWeight();
            $zip = $request->getDestPostcode();

            $userGroupId = "-1";
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Get group Id
                $userGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            }

            $allCategories = array();
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $cartItems = $quote->getAllVisibleItems();
            foreach ($cartItems as $item) {
                $productId = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $allCategories = array_merge($allCategories, $product->getCategoryIds());
            }
            $allCategories = array_unique($allCategories);

            $model = Mage::getModel('caship/method')->getCollection()
            ->addFieldToFilter('status', 1)
                ->addFieldToFilter('amount_min', array(array('lteq' => $orderAmount), array('eq' => 0)))
                ->addFieldToFilter('amount_max', array(array('gteq' => $orderAmount), array('eq' => 0)))
                ->addFieldToFilter('weight_min', array(array('lteq' => $weight), array('eq' => 0)))
                ->addFieldToFilter('weight_max', array(array('gteq' => $weight), array('eq' => 0)))
                ->addFieldToFilter('qty_min', array(array('lteq' => $quantity), array('eq' => 0)))
                ->addFieldToFilter('qty_max', array(array('gteq' => $quantity), array('eq' => 0)))
                ->addFieldToFilter('zip_min', array(array('lteq' => $zip), array('eq' => ''), array('null' => true)))
                ->addFieldToFilter('zip_max', array(array('gteq' => $zip), array('eq' => ''), array('null' => true)))
                ->addFieldToFilter('user_group_ids', array(array('eq' => ''), array('null' => true), array('like' => "%" . $userGroupId . "%")))
                ->addFieldToFilter(array('is_country_all', 'country_ids'), array(array('eq' => 0), array('like' => '%' . $request->getDestCountryId() . '%')));

            foreach ($allCategories as $cat) {
                $model->addFieldToFilter('product_categories', array(array('like' => "%," . $cat . ",%")));
            }

            if (count($model) == 0) {
                $model = Mage::getModel('caship/method')->getCollection()
                ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('amount_min', array(array('lteq' => $orderAmount), array('eq' => 0)))
                    ->addFieldToFilter('amount_max', array(array('gteq' => $orderAmount), array('eq' => 0)))
                    ->addFieldToFilter('weight_min', array(array('lteq' => $weight), array('eq' => 0)))
                    ->addFieldToFilter('weight_max', array(array('gteq' => $weight), array('eq' => 0)))
                    ->addFieldToFilter('qty_min', array(array('lteq' => $quantity), array('eq' => 0)))
                    ->addFieldToFilter('qty_max', array(array('gteq' => $quantity), array('eq' => 0)))
                    ->addFieldToFilter('zip_min', array(array('lteq' => $zip), array('eq' => ''), array('null' => true)))
                    ->addFieldToFilter('zip_max', array(array('gteq' => $zip), array('eq' => ''), array('null' => true)))
                    ->addFieldToFilter('user_group_ids', array(array('eq' => ''), array('null' => true), array('like' => "%" . $userGroupId . "%")))
                    ->addFieldToFilter('product_categories', array(array('eq' => ''), array('null' => true)))
                    ->addFieldToFilter(array('is_country_all', 'country_ids'), array(array('eq' => 0), array('like' => '%' . $request->getDestCountryId() . '%')));
            }
            /*$model->getSelect()->order("sort ASC");*/
            /*$model->getSelect()->columns(
                array(
                    'storeid'=> new Zend_Db_Expr('max(store_id)')
                    )
                );
            $model->getSelect()->group('upsmethod_id');*/
        } else {
            $model = Mage::getModel('caship/method')->getCollection()
                ->addFieldToFilter('status', 1);
            /*$model->getSelect()->order("sort ASC");*/
        }

        $freeMethod = 0;
        foreach ($model AS $method) {
            if ($method->getDinamicPrice() == 0 && $method->getPrice() <= 0) {
                $freeMethod++;
            }
        }

        foreach ($model AS $method) {
            if (($request->getDestCountryId() && $request->getDestPostcode() /*&& $request->getDestCity()*/)) {
                $methodEnd = $this->_getStandardShippingRate($request, $method, $freeMethod);
                if ($methodEnd !== FALSE) {
                    $result->append($methodEnd);
                }
            }
        }

        return $result;
    }

    protected
    function _getStandardShippingRate(Mage_Shipping_Model_Rate_Request $request, $method, $freeMethod = 0)
    {
        $storeId = 0;
        
        $this->configMethod = Mage::getModel('caship/config_upsmethod');

        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);

        if (strlen(Mage::getStoreConfig('carriers/caship/title')) > 0) {
            $rate->setCarrierTitle(Mage::getStoreConfig('carriers/caship/title'));
        }

        $mTitle = $method->getName();

        $ratePrice = 0;

        if ($method->getDinamicPrice() == 1) {
            if ($method->getCompanyType() == 'ups' || $method->getCompanyType() == 'upsinfomodus') {
                if ($this->ratesUps === NULL) {
                    $ups = Mage::getModel('caship/ups');
                    $ups = Mage::helper('caship/ups')->setParams($ups, $method, $request);
                    $this->ratesUps = $ups->getShipRate();
                }
                $otherMethodsId = '';
                $directionType = (int)$method->getDirectionType();
                for ($i = $directionType; $i >= 1; $i--) {
                    $methodId = 0;
                    switch ($i) {
                        case 1:
                            $methodId = $method->getUpsmethodId_3();
                            $otherMethodsId .= '-' . $method->getUpsmethodId_3();
                            break;
                        case 2:
                            $methodId = $method->getUpsmethodId_2();
                            $otherMethodsId .= '-' . $method->getUpsmethodId_2();
                            break;
                        case 3:
                            $methodId = $method->getUpsmethodId();
                            $otherMethodsId .= '-' . $method->getUpsmethodId();
                            break;
                    }
                    if (isset($this->ratesUps[$methodId])) {
                        $ratecode2 = $this->ratesUps[$methodId];
                        if (isset($ratecode2['price'])) {
                            $ratePrice2 = (float)$ratecode2['price'];
                            $rateCurrency = (string)$ratecode2['currency'];
                            $to = Mage::app()->getStore()->getCurrentCurrencyCode();
                            if ($rateCurrency != $to) {
                                $baseCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
                                $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
                                $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrency, array_values($allowedCurrencies));
                                if (isset($rates[$rateCurrency]) && $rates[$rateCurrency] > 0) {
                                    $basePrice = $ratePrice2 / $rates[$rateCurrency];
                                } else {
                                    $basePrice = $ratePrice2 / str_replace(",", ".", Mage::getStoreConfig('carriers/caship/rate'));
                                }
                                if ($baseCurrency != $to) {
                                    $ratePrice2 = Mage::helper('directory')->currencyConvert($ratePrice2, $baseCurrency, $to);
                                } else {
                                    $ratePrice2 = $basePrice;
                                }
                            }
                            $ratePrice += $ratePrice2;

                            /*if ($method->getTimeintransit() == 1 && isset($ratecode2['day'])) {
                                if ($method->getTitShowFormat() === 'days') {
                                    $mTitle .= ' (' . ($ratecode2['day']['days'] + $method->getAddday()) . Mage::helper('adminhtml')->__(' day(s)') . ')';
                                } else if ($method->getTitShowFormat() === 'datetime') {
                                    $dateFormat = new \Datetime($ratecode2['day']['datetime']['date']);
                                    $mTitle .= ' (' . $dateFormat->format('d') . ' ' . Mage::helper('adminhtml')->__($dateFormat->format('F')) . ' ' . $dateFormat->format('Y') . ' ' . substr($ratecode2['day']['datetime']['time'], 0, -3) . ')';
                                }
                            }*/
                        } else {
                            Mage::log($this->ratesUps, null, 'caship_debug.log');
                        }
                    } else {
                        Mage::log($this->ratesUps, null, 'caship_debug.log');
                        return FALSE;
                    }
                }
                $rate->setMethod($method->getId() . '_' . $method->getCompanyType() . '_' . $method->getUpsmethodId() . $otherMethodsId);
            }
            if ($ratePrice <= 0) {
                Mage::log("Price: 0", null, 'caship_debug.log');
                return false;
            }
            if ($method->getAddedValue() != 0 && $method->getAddedValue() != "") {
                if ($method->getAddedValueType() == 'static') {
                    $ratePrice += (float)str_replace(",", ".", $method->getAddedValue());
                } else {
                    $ratePrice += ($ratePrice / 100) * str_replace(",", ".", $method->getAddedValue());
                }
            }
        } else {
            $ratePrice = $method->getPrice();
            if ($method->getCompanyType() == 'ups' || $method->getCompanyType() == 'upsinfomodus') {
                $otherMethodsId = '';
                for ($i = 0; $i < $method->getDirectionType(); $i++) {
                    switch ($i) {
                        case 2:
                            $otherMethodsId .= '-' . $method->getUpsmethodId2();
                            break;
                        case 3:
                            $otherMethodsId .= '-' . $method->getUpsmethodId3();
                            break;
                    }
                }
                $rate->setMethod($method->getId() . '_' . $method->getCompanyType() . '_' . $method->getUpsmethodId() . $otherMethodsId);

            }
        }
        $rate->setMethodTitle($mTitle);
        $rate->setPrice($ratePrice);
        $rate->setCost(0);
        return $rate;
    }

    public
    function getAllowedMethods()
    {
        $storeId = 1;
        
        $arrMethods = array();
        $model = Mage::getModel('caship/method')->getCollection()
        ->addFieldToFilter('status', 1);
        foreach ($model AS $method) {
            /*if ($method->getDinamicPrice() == 1) {*/
            switch ($method->getCompanyType()) {
                case 'dhl':
                case 'dhlinfomodus':
                    $arrMethods[$method->getId() . '_' . $method->getCompanyType() . '_' . $method->getDhlmethodId()] = $method->getTitle();
                    break;
                case 'ups':
                case 'upsinfomodus':
                    $arrMethods[$method->getId() . '_' . $method->getCompanyType() . '_' . $method->getUpsmethodId()] = $method->getTitle();
                    break;
                default:
                    $arrMethods[$method->getId() . '_' . $method->getCompanyType()] = $method->getTitle();
                    break;
            }
            /*} else {
                $arrMethods[$method->getId() . '_' . $method->getCompanyType()] = $method->getTitle();
            }*/
        }
        return $arrMethods;
    }
}