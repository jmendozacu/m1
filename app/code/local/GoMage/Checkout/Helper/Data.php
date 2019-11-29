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

require_once(Mage::getBaseDir('lib') . DS . 'GoMage' . DS . 'MobileDetect' . DS . 'Checkout_Mobile_Detect.php');

class GoMage_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $mode;
    protected $country_id;

    function getConfigData($node)
    {
        return Mage::getStoreConfig('gomage_checkout/' . $node);
    }

    function getCheckoutMode()
    {
        if (is_null($this->mode)) {
            if (Mage::getSingleton('gomage_checkout/type_onestep')->getQuote()->isAllowedGuestCheckout()) {
                $this->mode = intval($this->getConfigData('registration/mode'));
            } else {
                $this->mode = 1;
            }
        }
        return $this->mode;
    }

    function getGeoipRecord()
    {
        return GeoIP_Core::getInstance(Mage::getBaseDir('media') . "/geoip/GeoLiteCity.dat", GeoIP_Core::GEOIP_STANDARD)->geoip_record_by_addr(Mage::helper('core/http')->getRemoteAddr());
    }

    function getDefaultCountryId()
    {
        if (is_null($this->country_id)) {
            if (Mage::getStoreConfig('gomage_checkout/geoip/geoip_enabled') && file_exists(Mage::getBaseDir('media') . "/geoip/GeoLiteCity.dat") && extension_loaded('mbstring')) {
                try {
                    $this->country_id = GeoIP_Core::getInstance(Mage::getBaseDir('media') . "/geoip/GeoLiteCity.dat", GeoIP_Core::GEOIP_STANDARD)->geoip_country_code_by_addr(Mage::helper('core/http')->getRemoteAddr());
                } catch (Exception $e) {
                    echo $e;
                }
            }

            if (!$this->country_id) {
                $this->country_id = Mage::getStoreConfig('gomage_checkout/general/default_country');
                if (!$this->country_id) {
                    $this->country_id = Mage::getStoreConfig('general/country/default');
                }
            }
        }
        return $this->country_id;
    }

    function getDefaultShippingMethod()
    {
        $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        $address->setCollectShippingRates(true)->collectShippingRates();
        $rates = $address->getGroupedAllShippingRates();

        if (count($rates) == 1) {
            foreach ($rates as $rate_code => $methods) {
                if (count($methods) == 1) {
                    foreach ($methods as $method) {

                        return $method->getCode();
                    }
                } else {
                    return $this->getConfigData('general/default_shipping_method');
                }
            }
        } else {
            return $this->getConfigData('general/default_shipping_method');
        }
    }

    function getDefaultPaymentMethod()
    {
        return $this->getConfigData('general/default_payment_method');
    }

    function getActivePaymentMethods($store = null)
    {
        $methods = array();
        $config  = Mage::getStoreConfig('payment', $store);

        foreach ($config as $code => $methodConfig) {

            if (isset($methodConfig['model']) && $methodConfig['model']) {
                if (isset($methodConfig['group']) && $methodConfig['group'] == 'mbookers' && Mage::getStoreConfigFlag('moneybookers/' . $code . '/active', $store)) {
                    $method          = $this->_getPaymentMethod($code, $methodConfig);
                    $method['group'] = 'mbookers';
                    $methods[$code]  = $method;
                } elseif ($methodConfig['model'] == 'googlecheckout/payment') {
                    if (Mage::getStoreConfigFlag('google/checkout/active', $store)) {
                        $method         = $this->_getPaymentMethod($code, $methodConfig);
                        $methods[$code] = $method;
                    }
                } elseif (isset($methodConfig['group']) && $methodConfig['group'] == 'payone') {
                    $method = $this->_getPaymentMethod($code, $methodConfig);
                    if ($method && $method->isAvailable()) {
                        $methods[$code] = $method;
                    }
                } elseif (Mage::getStoreConfigFlag('payment/' . $code . '/active', $store)) {
                    $method          = $this->_getPaymentMethod($code, $methodConfig);
                    $method['group'] = '';
                    $methods[$code]  = $method;
                }
            }
        }

        return $methods;
    }

    protected function _getPaymentMethod($code, $config, $store = null)
    {
        $modelName = $config['model'];
        $method    = Mage::getModel($modelName);

        if ($method) {
            $method->setId($code)->setStore($store);
        }
        return $method;
    }

    function getVatBaseCountryMode()
    {
        return $this->getConfigData('vat/base_country');
    }

    function getVatWithinCountryMode()
    {
        return $this->getConfigData('vat/if_not_base_country');
    }

    function getTaxCountries()
    {
        $rule_ids = Mage::helper('gomage_checkout')->getConfigData('vat/rule');
        $rule_ids = array_filter(explode(',', $rule_ids));
        if (count($rule_ids)) {
            $resource   = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('read');
            $q          = sprintf('SELECT DISTINCT(`tax_country_id`) FROM `%s` WHERE `tax_calculation_rate_id` IN (SELECT `tax_calculation_rate_id` FROM `%s` WHERE `tax_calculation_rule_id` in (%s) )',
                $resource->getTableName('tax_calculation_rate'),
                $resource->getTableName('tax_calculation'),
                implode(',', $rule_ids)
            );
            return (array)$connection->fetchCol($q);
        }

        return array();
    }

    function getIsAnymoreVersion($major, $minor, $revision = 0)
    {
        $version_info = Mage::getVersionInfo();

        if ($version_info['major'] > $major) {
            return true;
        } elseif ($version_info['major'] == $major) {
            if ($version_info['minor'] > $minor) {
                return true;
            } elseif ($version_info['minor'] == $minor) {
                if ($version_info['revision'] >= $revision) {
                    return true;
                }
            }
        }

        return false;
    }

    function isEnterprise()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        return in_array('Enterprise_Enterprise', $modules);
    }

    function isCompatibleDevice()
    {

        $detect = new Checkout_Mobile_Detect();
        if (!$detect->isMobile()) {
            return (bool)$this->getConfigData('device/desktop');
        }
        if ($detect->isTablet()) {
            $devices = explode(',', $this->getConfigData('device/tablet'));
        } else {
            $devices = explode(',', $this->getConfigData('device/smartphone'));
        }

        if ($detect->isAndroidOS()) {
            return in_array(GoMage_Checkout_Model_Adminhtml_System_Config_Source_Device::ANDROID, $devices);
        }
        if ($detect->isBlackBerryOS()) {
            return in_array(GoMage_Checkout_Model_Adminhtml_System_Config_Source_Device::BLACKBERRY, $devices);
        }
        if ($detect->isiOS()) {
            return in_array(GoMage_Checkout_Model_Adminhtml_System_Config_Source_Device::IOS, $devices);
        }

        return in_array(GoMage_Checkout_Model_Adminhtml_System_Config_Source_Device::OTHER, $devices);

    }

    function isLefttoRightWrite()
    {
        return in_array(Mage::app()->getLocale()->getLocaleCode(), array('ar_DZ', 'ar_EG', 'ar_KW', 'ar_MA',
                'ar_SA', 'he_IL', 'fa_IR')
        );
    }

    function getCountriesStatesRequired()
    {
        $result = array();

        if ($this->getConfigData('address_fields/region') == 'req') {
            $country_collection = Mage::helper('directory')->getCountryCollection();
            foreach ($country_collection as $country) {
                $result[] = $country->getCountryId();
            }
        }
        return Mage::helper('core')->jsonEncode($result);

    }

    function formatColor($value)
    {
        if ($value = preg_replace('/[^a-zA-Z0-9\s]/', '', $value)) {
            $value = '#' . $value;
        }
        return $value;
    }
}
