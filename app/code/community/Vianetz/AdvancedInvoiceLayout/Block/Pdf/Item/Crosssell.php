<?php
/**
 * AdvancedInvoiceLayout Pdf Crosssell Item block
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
 * @package     Vianetz\AdvancedInvoiceLayout
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
* @copyright   Copyright (c) 2006-18 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */
/**
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Invoice_Item
 *
 */
class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Crosssell extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Abstract
{
    /**
     * Init template.
     */
    protected function _construct()
    {
        $this->setTemplate($this->_getTemplateFilePath('item' . DS . 'crosssell.phtml'));

        // Do not call the parent constructor as getItem() method returns different values..
        // @todo refactor this
        $this->_itemContainerModel = Mage::getModel('advancedinvoicelayout/pdf_container_item_default')
            ->setItem($this->getItem());
    }

    public function getOrder()
    {
        return $this->getData('order');
    }

    /**
     * Return relative product image URL.
     *
     * @return string
     */
    public function getProductImagePath()
    {
        $imageUrl = $this->getProduct()->getSmallImageUrl();
        if (empty($imageUrl) === true) {
            return '';
        }

        $imageUrl = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), Mage_Core_Model_Store::URL_TYPE_MEDIA . '/', $imageUrl);

        return $imageUrl;
    }

    /**
     * Get short description for product.
     *
     * @return string
     */
    public function getProductShortDescription()
    {
        $shortDescription = strip_tags(htmlspecialchars_decode($this->getItem()->getShortDescription()));
        return $shortDescription;
    }
}
