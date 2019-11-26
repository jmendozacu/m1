<?php
/**
 * Vianetz Core License Update Block
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_Core
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 */
/**
 * Class Vianetz_Core_Block_Config
 * @method string getClass()
 * @method int getProductId()
 */
class Vianetz_Core_Block_Config extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * This is the main method that is called from backend.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$this->setElement($element);

        $configId = str_replace('_', '/', $element->getId());
        $config = explode(':', Mage::getStoreConfig($configId));
        $this->setProductId($config[0]);
        $this->setClass($config[1]);

        $html = '<p>' . $this->__('The usage of this extension is subject to our <a href="%s" target="_blank">License Agreement</a>.', $this->getLicenseUrl()) . '</p>';

    	$html .= $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setLabel($this->__('Submit a Support Ticket'))
                    ->setOnClick("window.open('" . $this->getContactUrl() .  "')")
                    ->setDisabled(false)
                    ->toHtml();

    	return $html;
    }

    /**
     * @return string
     */
    private function getContactUrl()
    {
        $url = "https:&#47;&#47;www.vianetz.com/en/contacts/?utm_source=magentobackend&utm_medium=postrequest&utm_campaign=TicketRequest%s&request=%s&config=%s&version=%s&class=%s&product=%s";
        return sprintf($url, $this->getClass(), $this->getFileVersion(), $this->getConfigurationUrl(), $this->getModuleVersion(), $this->getClass(), $this->getProductId());
    }

    /**
     * @return string
     */
    private function getModuleVersion()
    {
        return (string)Mage::getConfig()->getModuleConfig($this->getClass())->version;
    }

    /**
     * @return string
     */
    private function getConfigurationUrl()
    {
        $url = parse_url(Mage::getBaseUrl());
        return $url['host'];
    }

    /**
     * @return string
     */
    private function getFileVersion()
    {
        $file = Mage::getBaseDir('app') . DS . 'code' . DS . 'community' . DS . 'Vianetz' . DS . 'Core' . DS . 'Block' . DS . 'Config.php';
        return md5_file($file);
    }

    /**
     * @return string
     */
    private function getLicenseUrl()
    {
        return 'https://www.vianetz.com/license';
    }
}
