<?php
/**
 * AdvancedInvoiceLayout Pdf Abstract block
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
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract
 * @method Mage_Sales_Model_Abstract getSource()
 * @method bool getIsPrintedInAdmin()
 */
abstract class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract extends Mage_Core_Block_Template
{
    /**
     * The default font family to use if none is configured.
     *
     * @var string
     */
    const DEFAULT_FONT_FAMILY = 'Helvetica';

    /**
     * The default font size to use if none is configured.
     *
     * @var string
     */
    const DEFAULT_FONT_SIZE = 12;

    /**
     * The default theme name that is used if e.g. none is configured.
     *
     * @var string
     */
    const DEFAULT_THEME_NAME = 'default';

    /**
     * Holds the current customer so that it is not necessary to retrieve it each time.
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * @var null|Mage_GiftMessage_Model_Message
     */
    protected $_giftMessage;

    /**
     * We force to set frontend design area.
     *
     * @return string
     */
    public function getArea()
    {
        return Mage_Core_Model_App_Area::AREA_FRONTEND;
    }

    /**
     * Overwrite original method to never show template hints for PDF blocks because this will destroy our PDF file.
     *
     * @return boolean
     */
    public function getShowTemplateHints()
    {
        return false;
    }

    /**
     * Return order for invoice/shipment/creditmemo.
     *
     * @api
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getSource()->getOrder();
    }

    /**
     * Return configured sender address.
     *
     * @api
     *
     * @return string
     */
    public function getSenderAddress()
    {
        return $this->_getConfiguredFreeTextWithPlaceholders('advancedinvoicelayout/layout_header/sender_address');
    }

    /**
     * Return billing address.
     *
     * @api
     *
     * @return string
     */
    public function getBillingAddress()
    {
        $billingAddress = $this->getOrder()->getBillingAddress();
        return $billingAddress->format('advancedinvoicelayout_html');
    }

    /**
     * Return shipping address.
     *
     * @api
     *
     * @return string
     */
    public function getShippingAddress()
    {
        $shippingAddress = $this->getOrder()->getShippingAddress();
        if (empty($shippingAddress) === true) {
            return '';
        }

        return $shippingAddress->format('advancedinvoicelayout_html');
    }

    /**
     * Return tax vat number.
     *
     * @api
     *
     * @return string
     */
    public function getCustomerTaxVatNumber()
    {
        $isTaxvatEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/layout_header/show_taxvat');
        if ($isTaxvatEnabled === false) {
            return '';
        }

        $customerTaxvat = $this->getOrder()->getCustomerTaxvat();
        if (empty($customerTaxvat) === false) {
            return $customerTaxvat;
        }

        $billingTaxvat = $this->getOrder()->getBillingAddress()->getVatId();
        if (empty($billingTaxvat) === false) {
            return $billingTaxvat;
        }

        return '';
    }

    /**
     * Get creation date of source document (invoice/shipment/creditmemo).
     * 
     * @api
     *
     * @return string
     */
    public function getSourceDate()
    {
        return $this->helper('core')->formatDate($this->getSource()->getCreatedAt(), 'medium', false);
    }

    /**
     * Get order date.
     *
     * @api
     *
     * @return string
     */
    public function getOrderDate()
    {
        return $this->helper('core')->formatDate($this->getOrder()->getCreatedAt(), 'medium', false);
    }

    /**
     * Return customer increment id.
     *
     * @api
     *
     * @return string
     */
    public function getCustomerId()
    {
        $customerId = $this->getCustomer()->getIncrementId();
        if (empty($customerId) === true) {
            $customerId = $this->getCustomer()->getId();
        }

        return ltrim($customerId, '0');
    }

    /**
     * Return Customer Model for given order.
     *
     * @api
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer) === true) {
            $this->_customer = Mage::getModel('customer/customer')->load($this->getOrder()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Get payment method text block.
     *
     * If the info.phtml block of the payment method is empty we insert only the payment title (e.g. if not installed
     * correctly and invoice is printed in frontend).
     *
     * @api
     *
     * @param boolean $isShowFullTexts if set to true return complete payment information, otherwise there
     * are some special treatments e.g. for Billsafe where only title is shown.
     *
     * @return string
     */
    public function getPaymentMethodText($isShowFullTexts = false)
    {
        $paymentInfo = $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
            ->setIsSecureMode(true)
            ->toHtml();

        // Special Billsafe handling.
        if (trim($paymentInfo) == ''
            || Mage::getStoreConfigFlag('advancedinvoicelayout/general/show_full_payment_info_block', $this->getOrder()->getStore()) === false
            || ($isShowFullTexts === false && $this->helper('advancedinvoicelayout/payment')->isBillSafePayment($this->getOrder()) === true)) {
            $paymentInfo = $this->getOrder()->getPayment()->getMethodInstance()->getTitle();
        }

        return $paymentInfo;
    }

    /**
     * This method should not be used any longer.
     *
     * @deprecated
     * @see Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract::isShowSecondaryAddress()
     *
     * @return bool
     */
    public function isShowShippingAddress()
    {
        return $this->isShowSecondaryAddress();
    }

    /**
     * Check if the non-primary address should be shown if addresses are identical.
     *
     * For invoices and creditmemos the secondary address is the shipping address, for shipments it's the billing address.
     *
     * @return boolean
     */
    public function isShowSecondaryAddress()
    {
        $isHideSecondaryAddressIfEqual = Mage::getStoreConfigFlag($this->_getBlockConfigPath('hide_non_primary_address'), $this->getOrder()->getStore());
        $isAddressEqual = $this->helper('advancedinvoicelayout/address')->isAddressEqual($this->getOrder()->getBillingAddress(), $this->getOrder()->getShippingAddress());

        if ($isHideSecondaryAddressIfEqual === false || $isAddressEqual === false) {
            return true;
        }

        return false;
    }

    /**
     * Return the theme-specific path to css file.
     *
     * @api
     *
     * @return string
     */
    public function getThemeCssFile()
    {
        $fileName = 'advancedinvoicelayout/' . $this->getThemeName() . '/css/pdf.css';
        return $this->_getSkinPath($fileName, array('_area' => Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * Return the theme-specific path to header css file.
     *
     * @api
     *
     * @return string
     */
    public function getHeaderCssFile()
    {
        $fileName = 'advancedinvoicelayout/' . $this->getThemeName() . '/css/header.css';
        return $this->_getSkinPath($fileName, array('_area' => Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * Return the path to the custom css file.
     *
     * @api
     * @since 2.5.3
     *
     * @return string
     */
    public function getCustomCssFile()
    {
        $fileName = 'advancedinvoicelayout/' . $this->getThemeName() . '/css/custom.css';
        return $this->_getSkinPath($fileName, array('_area' => Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * Return configured theme name.
     *
     * @api
     *
     * @return string
     */
    public function getThemeName()
    {
        $configuredTheme = Mage::getStoreConfig('advancedinvoicelayout/general/theme', $this->getOrder()->getStore());
        if (empty($configuredTheme) === true) {
            return self::DEFAULT_THEME_NAME;
        }

        return $configuredTheme;
    }

    /**
     * Get coupon code of order.
     *
     * @api
     *
     * @return string
     */
    public function getCouponCode()
    {
        $isCouponCodeEnabled = Mage::getStoreConfigFlag($this->_getBlockConfigPath('show_coupon_code'), $this->getOrder()->getStore());
        if ($isCouponCodeEnabled === false) {
            return '';
        }

        return trim($this->getOrder()->getCouponCode());
    }

    /**
     * Get payment info for method Billsafe.
     *
     * @api
     *
     * @return string
     */
    public function getBillsafeFreetext()
    {
        if ($this->helper('advancedinvoicelayout/payment')->isBillSafePayment($this->getOrder()) === false) {
            return '';
        }

        return $this->getPaymentMethodText(true);
    }

    /**
     * Return Gift Message From Customer.
     *
     * @api
     *
     * @return null|Mage_GiftMessage_Model_Message
     */
    public function getGiftMessage()
    {
        $giftMessageId = $this->getOrder()->getGiftMessageId();
        $isGiftMessageEnabled = Mage::getStoreConfig($this->_getBlockConfigPath('show_giftmsg'), $this->getOrder()->getStore());
        if ($isGiftMessageEnabled === false || empty($giftMessageId) === true) {
            return null;
        }

        if (empty($this->_giftMessage) === true) {
            $this->_giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId);
        }

        return $this->_giftMessage;
    }

    /**
     * Check if customer comments for source (invoice/shipment/creditmemo) are enabled.
     *
     * @api
     *
     * @return bool
     */
    public function isShowCustomerSourceComments()
    {
        return Mage::getStoreConfigFlag($this->_getBlockConfigPath('show_customer_comments'), $this->getOrder()->getStore());
    }

    /**
     * Check if customer comments for order are enabled.
     *
     * @api
     *
     * @return bool
     */
    public function isShowCustomerOrderComments()
    {
        return Mage::getStoreConfigFlag('advancedinvoicelayout/general/show_order_comments', $this->getOrder()->getStore());
    }

    /**
     * Return customer group specific free text (if available).
     *
     * @api
     *
     * @return string
     */
    public function getCustomerGroupSpecificFreetext()
    {
        $customerGroupId = $this->getOrder()->getCustomerGroupId();
        /** @var Mage_Customer_Model_Group $customerGroup */
        $customerGroup = Mage::getModel('customer/group')->load($customerGroupId);

        if ($customerGroup->isEmpty() === true) {
            return '';
        }

        $freeText = htmlentities($customerGroup->getVianetzAdvancedinvoicelayoutCustomerGroupFreetext());
        if (empty($freeText) === true) {
            return '';
        }

        return $freeText;
    }

    /**
     * Return free text that can be configured in System->Configuration.
     *
     * @api
     *
     * @return string
     */
    public function getConfiguredFreeTextWithPlaceholders()
    {
        return $this->_getConfiguredFreeTextWithPlaceholders($this->_getBlockConfigPath('freetext'));
    }

    /**
     * Return relative path to logo image.
     * 
     * @api
     *
     * @return string
     */
    public function getLogoPath()
    {
        $imageFileName = Mage::getStoreConfig('sales/identity/logo', $this->getSource()->getOrder()->getStore());
        if (empty($imageFileName) === false) {
            $imageDirectory = Mage_Core_Model_Store::URL_TYPE_MEDIA . DS . 'sales' . DS . 'store' . DS . 'logo' . DS . $imageFileName;
            if (is_file($imageDirectory) === true) {
                return $imageDirectory;
            }
        }

        return '';
    }

    /**
     * Get crosssell products collection.
     *
     * @api
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    public function getCrosssellProducts()
    {
        return Mage::getModel('advancedinvoicelayout/pdf_container_crosssells')
            ->setSource($this->getSource())
            ->getProductCollection();
    }

    /**
     * Returns if the header should be shown on every page or only on the first one.
     * 
     * @api
     *
     * @return boolean
     */
    public function isShowHeaderOnEveryPage()
    {
        return Mage::getStoreConfigFlag('advancedinvoicelayout/page_margins/show_header_on_every_page');
    }

    /**
     * Return configured font family.
     *
     * @api
     *
     * @return string
     */
    public function getFontFamily()
    {
        $configuredFontFamily = Mage::getStoreConfig('advancedinvoicelayout/general/font_family');
        if (empty($configuredFontFamily) === true) {
            $configuredFontFamily = self::DEFAULT_FONT_FAMILY;
        }

        return $configuredFontFamily;
    }

    /**
     * Return configured font size.
     *
     * @api
     *
     * @return string
     */
    public function getFontSize()
    {
        $configuredFontSize = (int)Mage::getStoreConfig('advancedinvoicelayout/general/font_size');
        if (empty($configuredFontSize) === true || (int)$configuredFontSize < 0) {
            $configuredFontSize = self::DEFAULT_FONT_SIZE;
        }

        return $configuredFontSize . 'px';
    }

    /**
     * Return configured top margin in pixels.
     *
     * @api
     *
     * @param boolean $isFirstPage
     *
     * @return string
     */
    public function getTopMargin($isFirstPage = false)
    {
        if ($isFirstPage === true) {
            return (int)Mage::getStoreConfig('advancedinvoicelayout/page_margins/top_margin_first_page');
        }

        return (int)Mage::getStoreConfig('advancedinvoicelayout/page_margins/top_margin');
    }

    /**
     * Return configured bottom margin in pixels.
     *
     * @api
     *
     * @return string
     */
    public function getBottomMargin()
    {
        return (int)Mage::getStoreConfig('advancedinvoicelayout/page_margins/bottom_margin');
    }

    /**
     * Return configured left margin in pixels.
     *
     * @api
     *
     * @return integer
     */
    public function getLeftMargin()
    {
        return (int)Mage::getStoreConfig('advancedinvoicelayout/page_margins/left_margin');
    }

    /**
     * Return configured right margin in pixels.
     * 
     * @api
     *
     * @return integer
     */
    public function getRightMargin()
    {
        return (int)Mage::getStoreConfig('advancedinvoicelayout/page_margins/right_margin');
    }

    /**
     * Return configured text for salutation text.
     * 
     * @api
     *
     * @return string
     */
    public function getSalutationText()
    {
        return $this->_getConfiguredFreeTextWithPlaceholders($this->_getBlockConfigPath('salutation_text'));
    }

    /**
     * Format price value according to locale settings.
     *
     * Additionally if the source is a creditmemo we may show a negative sign in front.
     *
     * @api
     *
     * @param float $priceValue The price value to format.
     * @param boolean $isReturnZero If set to false an empty string will be returned if price is zero (instead of formatted 0,00)
     *
     * @return string
     */
    public function formatPrice($priceValue, $isReturnZero = true)
    {
        if ($priceValue === null || $isReturnZero === false && $priceValue == 0) {
            return '';
        }

        $signText = '';
        $showNegativeSign = Mage::getStoreConfigFlag('advancedinvoicelayout/creditmemo/show_all_amounts_negative');
        if (($this->getSource() instanceof Mage_Sales_Model_Order_Creditmemo || $this->getItem() instanceof Mage_Sales_Model_Order_Creditmemo_Item)
            && $showNegativeSign === true) {
            $signText = '-';
        }

        return '<nobr>' . $signText . $this->getOrder()->formatPriceTxt($priceValue) . '</nobr>';
    }

    /**
     * Get Customer Email address if enabled.
     *
     * @api
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        $isCustomerEmailEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/layout_header/show_customer_email');
        if ($isCustomerEmailEnabled === true) {
            return $this->getOrder()->getCustomerEmail();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getOrder()->getBillingAddress()->getName();
    }

    /**
     * Get footer page count template html.
     *
     * @api
     *
     * @return string
     */
    public function getFooterPageCountHtml()
    {
        return $this->getLayout()->createBlock('advancedinvoicelayout/pdf_footer_pagecount', '', array('source' => $this->getSource()))
            ->toHtml();
    }

    /**
     * Return all source (= invoice/shipment/creditmemo) items.
     *
     * @api
     *
     * @see Mage_Sales_Model_Order_Pdf_Invoice::getPdf() for logic
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        foreach ($this->getSource()->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Return html contents for single crosssell item.
     *
     * @api
     *
     * @param Mage_Catalog_Model_Product $crosssellItem
     *
     * @return string
     */
    public function getCrosssellItemHtml(Mage_Catalog_Model_Product $crosssellItem)
    {
        return $this->getLayout()
            ->createBlock(
                'advancedinvoicelayout/pdf_item_crosssell',
                '',
                array('item' => $crosssellItem, 'order' => $this->getSource()->getOrder())
            )
            ->toHtml();
    }

    /**
     * Returns the additional checkout attributes created by the AITOC Checkout Fields Manager extension.
     *
     * We first check the method getInvoiceCustomData() which retrieves the values from the database and as
     * a fallback we check the session content with getSessionCustomData(). The second step is required because the custom order data is saved
     * after the order save and therewith e.g. the AutomaticInvoice extension would not be able to retrieve the Checkout
     * Attributes in frontend correctly.
     * If the extension is not installed or enabled an empty array is returned.
     * 
     * @api
     *
     * @return array
     */
    public function getCheckoutAttributes()
    {
        $isEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/invoice/show_checkout_attributes');
        if ($isEnabled === false || Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields') === false) {
            return array();
        }

        /** @var Aitoc_Aitcheckoutfields_Model_Aitcheckoutfields $aitocCheckoutFieldsModel */
        $aitocCheckoutFieldsModel = Mage::getModel('aitcheckoutfields/aitcheckoutfields');

        $customDataArray = $aitocCheckoutFieldsModel->getInvoiceCustomData($this->getOrder()->getId(), $this->getOrder()->getStoreId());
        if (empty($customDataArray) === false && is_array($customDataArray) === true) {
            return $customDataArray;
        }

        // Check the session content as fallback.
        $customDataArray = $aitocCheckoutFieldsModel->getSessionCustomData('onepage', $this->getOrder()->getStoreId(), null);
        if (empty($customDataArray) === false && is_array($customDataArray) === true) {
            return $customDataArray;
        }

        return array();
    }

    /**
     * Returns true if showing taxes is enabled, false otherwise.
     *
     * @api
     *
     * @return boolean
     */
    public function isShowTaxes()
    {
        return Mage::getStoreConfigFlag('advancedinvoicelayout/general/show_taxes');
    }

    /**
     * Check whether current order has a related recurring profile.
     *
     * @return boolean
     */
    public function isRecurringProfile()
    {
        $recurringProfile = $this->__getRecurringProfileModel();
        return (empty($recurringProfile) === false);
    }

    /**
     * @return null|string
     */
    public function getRecurringProfileStartDate()
    {
        $recurringProfile = $this->__getRecurringProfileModel();
        if (empty($recurringProfile) === true) {
            return null;
        }

        return $this->helper('core')->formatDate($recurringProfile->getStartDatetime(), 'medium', false);
    }

    /**
     * @return null|string
     */
    public function getRecurringProfilePeriod()
    {
        $recurringProfile = $this->__getRecurringProfileModel();
        if (empty($recurringProfile) === true) {
            return null;
        }

        return $recurringProfile->getPeriodFrequency() . ' ' . $this->__($recurringProfile->getPeriodUnit());
    }

    /**
     * Get store config path for current block. This method should be implemented by the individual block document types (invoice/shipment/..).
     *
     * @param string $configPath
     *
     * @return string
     */
    protected function _getBlockConfigPath($configPath)
    {
        return '';
    }

    /**
     * Replace placeholder fields in free text.
     * We do not use the default Magento filter for performance reasons and because the date operations cannot be handled.
     *
     * @param string $text
     *
     * @return string
     */
    protected function _evalTextPlaceholder($text)
    {
        if (!$this->getOrder() instanceof Mage_Sales_Model_Order) {
            return $text;
        }

        $templateFilter = Mage::getModel('advancedinvoicelayout/template_filter')
                ->setSource($this->getSource())
                ->init();

        return $templateFilter->filter($text);
    }

    /**
     * Return configured text for config path and replace html chars and newlines.
     *
     * @param string $configPath
     *
     * @return string
     */
    protected function _getConfiguredFreeTextWithPlaceholders($configPath)
    {
        $freeText = Mage::getStoreConfig($configPath, $this->getOrder()->getStore());
        $freeText = nl2br($freeText);

        return $this->_evalTextPlaceholder($freeText);
    }

    /**
     * Return file path relative to dompdf base directory (= Magento base directory).
     *
     * Relative paths are used instead of URLs as this is much faster e.g. for css.
     *
     * @param string $pathString
     * @param array $params
     *
     * @return string
     */
    protected function _getSkinPath($pathString, array $params = array())
    {
        $url = $this->getSkinUrl($pathString, $params);
        return str_replace(Mage::getBaseUrl('skin'), 'skin/', $url);
    }

    /**
     * This method simplifies usage of template file paths by only requiring the file path below the theme folder.
     *
     * @param string $templateFilePath Part of the template file below the theme folder.
     *
     * @return string
     */
    protected function _getTemplateFilePath($templateFilePath)
    {
        return 'advancedinvoicelayout' . DS . $this->getThemeName() . DS . $templateFilePath;
    }

    /**
     * @return \Mage_Sales_Model_Recurring_Profile
     */
    protected function __getRecurringProfileModel()
    {
        $profileId = Mage::getResourceModel('advancedinvoicelayout/recurring_profile')->getProfileIdByOrderId($this->getOrder()->getId());
        if (empty($profileId) === true) {
            return null;
        }

        /** @var Mage_Sales_Model_Recurring_Profile $recurringProfile */
        $recurringProfile = Mage::getModel('sales/recurring_profile')->load($profileId);

        return $recurringProfile;
    }
}
