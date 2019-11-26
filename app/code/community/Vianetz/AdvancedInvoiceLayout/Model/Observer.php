<?php
/**
 * AdvancedInvoiceLayout Observer
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
class Vianetz_AdvancedInvoiceLayout_Model_Observer
{
    /**
     * The default sort order for the buttons.
     *
     * As Magento does group the button arrays by sort order the numeric here must be unique. By default Magento uses
     * count($this->_buttons[$level]) * 10) but as we are replacing existing buttons there may be situations where we get
     * buttons with the same sort order and therewith only one of them is displayed.
     * @see Mage_Adminhtml_Block_Widget_Container::_addButton()
     *
     * @var integer
     */
    const BUTTON_DEFAULT_SORT_ORDER = 34;

    /**
     * Save customer group specific free text after saving of customer group.
     * Event: controller_action_postdispatch_adminhtml_customer_group_save
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function saveInvoiceFreeTextOnCustomerGroupSave(Varien_Event_Observer $observer)
    {
        $controllerAction = $observer->getEvent()->getControllerAction();
        $freeText = $controllerAction->getRequest()->getParam('vianetz_advancedinvoicelayout_customer_group_freetext');

        $customerGroupId = $controllerAction->getRequest()->getParam('id');
        if (empty($customerGroupId)) {
            return $this;
        }

        $customerGroup = Mage::getModel('customer/group')->load($customerGroupId);

        if ($customerGroup->getId() && isset($freeText)) {
            $customerGroup
                ->setVianetzAdvancedinvoicelayoutCustomerGroupFreetext($freeText)
                ->save();
        }

        return $this;
    }

    /**
     * Observer to add new print pdf buttons to admin order detail page.
     *
     * Event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addPrintPdfButtonToOrderView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false
        || $this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $blockInstance->getOrder();
        if ($order->hasInvoices() == false) {
            return $this;
        }

        $blockInstance->addButton(
            'printpdf',
            array(
                'label'     => Mage::helper('advancedinvoicelayout')->__('Print Invoice PDF'),
                'class'     => 'go',
                'onclick'   => 'setLocation(\'' . $this->getPrintInvoicesPdfForOrderUrl($blockInstance) . '\')'
            ),
            0,
            self::BUTTON_DEFAULT_SORT_ORDER
        );

        return $this;
    }

    /**
     * Observer to add new print pdf buttons to admin invoice detail page.
     *
     * Event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addPrintPdfButtonToInvoiceView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Invoice_View) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        // Remove existing button.
        $blockInstance->removeButton('print');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        $class = 'go';
        $onclick = 'setLocation(\'' . $this->getSignUrl($blockInstance) . '\')';

        if (Mage::getModel('advancedinvoicelayout/pdf_signature')->isSignedFileExistant($blockInstance->getInvoice()) === true) {
            $class .= ' disabled';
            $onclick = '';
        }

        if ($this->_isSignPdfInvoiceEnabled() === true) {
            $blockInstance->addButton(
                'signpdf',
                array(
                    'label' => Mage::helper('advancedinvoicelayout')->__('Sign Invoice PDF'),
                    'class' => $class,
                    'onclick' => $onclick,
                ),
                0,
                self::BUTTON_DEFAULT_SORT_ORDER-1
            );
        }

        $blockInstance->addButton(
            'printpdf',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print Invoice PDF'),
                'class' => 'go',
                'onclick' => 'setLocation(\'' . $this->getPrintInvoicePdfUrl($blockInstance) . '\')'
            ),
            0,
            self::BUTTON_DEFAULT_SORT_ORDER
        );

        return $this;
    }

    /**
     * Observer to add new print pdf buttons to admin shipment detail page.
     * Event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addPrintPdfButtonToShipmentView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        // Remove existing button.
        $blockInstance->removeButton('print');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        $blockInstance->addButton(
            'printpdf',
            array(
                'label'     => Mage::helper('advancedinvoicelayout')->__('Print Shipment PDF'),
                'class'     => 'go',
                'onclick'   => 'setLocation(\'' . $this->getPrintShipmentPdfUrl($blockInstance) . '\')'
            ),
            0,
            self::BUTTON_DEFAULT_SORT_ORDER
        );

        return $this;
    }

    /**
     * Observer to add new print pdf buttons to admin creditmemo detail page.
     * Event: core_block_abstract_prepare_layout_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addPrintPdfButtonToCreditmemoView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_View) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        // Remove existing button.
        $blockInstance->removeButton('print');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        $blockInstance->addButton(
            'printpdf',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print Creditmemo PDF'),
                'class' => 'go',
                'onclick' => 'setLocation(\'' . $this->getPrintCreditmemoPdfUrl($blockInstance) . '\')'
            ),
            0,
            self::BUTTON_DEFAULT_SORT_ORDER
        );

        return $this;
    }

    /**
     * Return url for signing invoice.
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance
     *
     * @return string
     */
    public function getSignUrl(Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance)
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advancedinvoicelayout_invoice/signpdf',
            array(
                'invoice_id' => $blockInstance->getInvoice()->getId()
            )
        );
    }

    /**
     * Return url for printing all invoice pdfs for a single order.
     *
     * @param Mage_Adminhtml_Block_Sales_Order_View $blockInstance
     *
     * @return string
     */
    public function getPrintInvoicesPdfForOrderUrl(Mage_Adminhtml_Block_Sales_Order_View $blockInstance)
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advancedinvoicelayout_invoice/pdfinvoicesbyorder',
            array(
                'order_ids' => $blockInstance->getOrder()->getId()
            )
        );
    }

    /**
     * Return url for printing invoice pdf.
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance
     *
     * @return string
     */
    public function getPrintInvoicePdfUrl(Mage_Adminhtml_Block_Sales_Order_Invoice_View $blockInstance)
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advancedinvoicelayout_invoice/printpdf',
            array(
                'invoice_id' => $blockInstance->getInvoice()->getId()
            )
        );
    }

    /**
     * Return url for printing shipment pdf.
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Shipment_View $blockInstance
     *
     * @return string
     */
    public function getPrintShipmentPdfUrl(Mage_Adminhtml_Block_Sales_Order_Shipment_View $blockInstance)
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advancedinvoicelayout_shipment/printpdf',
            array(
                'shipment_id' => $blockInstance->getShipment()->getId()
            )
        );
    }

    /**
     * Return url for printing creditmemo pdf.
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Creditmemo_View $blockInstance
     *
     * @return string
     */
    public function getPrintCreditmemoPdfUrl(Mage_Adminhtml_Block_Sales_Order_Creditmemo_View $blockInstance)
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/advancedinvoicelayout_creditmemo/printpdf',
            array(
                'creditmemo_id' => $blockInstance->getCreditmemo()->getId()
            )
        );
    }

    /**
     * This method is used for as adminhtml block before renderer to circumvent rewrites.
     * Event: adminhtml_block_html_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addCustomerGroupFreeTextarea(Varien_Event_Observer $observer)
    {
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Customer_Group_Edit_Form) {
            return $this;
        }

        Mage::getModel('advancedinvoicelayout/customer_group_freetext')
            ->addCustomerGroupFreeTextareaToBlock($blockInstance);

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin invoice grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintPdfActionToInvoiceGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Invoice_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfinvoices_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfinvoices',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Invoices'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_invoice/pdfinvoices')
            )
        );

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin invoice grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintPdfActionToShipmentGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Shipment_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfshipments_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfshipments',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Shipments'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_shipment/pdfshipments')
            )
        );

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin creditmemo grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintPdfActionToCreditmemoGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Shipment_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Creditmemo_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfcreditmemos_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfcreditmemos',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Creditmemos'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_creditmemo/pdfcreditmemos')
            )
        );

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin order grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintInvoicePdfActionToOrderGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfinvoices_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_INVOICE) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfinvoices',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Invoices'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_invoice/pdfinvoicesbyorder')
            )
        );

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin order grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintShipmentPdfActionToOrderGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfshipments_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_SHIPMENT) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfshipments',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Shipments'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_shipment/pdfshipmentsbyorder')
            )
        );

        return $this;
    }

    /**
     * Observer to add new mass print pdf action to admin order grid page.
     *
     * Event: adminhtml_block_html_before
     * (We cannot use core_block_abstract_prepare_layout_before because the massaction block is not generated there yet.)
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Observer
     */
    public function addMassPrintCreditmemoPdfActionToOrderGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Grid $blockInstance */
        $blockInstance =  $observer->getEvent()->getBlock();
        if (!$blockInstance instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            return $this;
        }

        if ($this->__getHelper()->isModuleActive(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->removeItem('pdfcreditmemos_order');

        if ($this->__getHelper()->isPrintActionAllowed(Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO) === false) {
            return $this;
        }

        $blockInstance->getMassactionBlock()->addItem(
            'advancedinvoicelayout_pdfcreditmemos',
            array(
                'label' => Mage::helper('advancedinvoicelayout')->__('Print PDF Creditmemos'),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/advancedinvoicelayout_creditmemo/pdfcreditmemosbyorder')
            )
        );

        return $this;
    }

    /**
     * Return whether PDF sign feature is enabled or not.
     *
     * @return bool
     */
    protected function _isSignPdfInvoiceEnabled()
    {
        return Mage::getStoreConfigFlag('advancedinvoicelayout/invoice/sign_enabled');
    }

    /**
     * @return \Vianetz_AdvancedInvoiceLayout_Helper_Data
     */
    private function __getHelper()
    {
        return Mage::helper('advancedinvoicelayout');
    }
}
