<?php
/**
 * AdvancedInvoiceLayout adminhtml abstract controller class
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
abstract class Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_AbstractController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var string
     */
    protected $_documentType;

    /**
     * @var Vianetz_AdvancedInvoiceLayout_Model_Pdf
     */
    protected $_pdf;

    /**
     * Default constructor initializes pdf model.
     */
    protected function _construct()
    {
        $this->_pdf = Mage::getModel('advancedinvoicelayout/pdf');
    }

    /**
     * Check if user is allowed to do current action.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::helper('advancedinvoicelayout')->isPrintActionAllowed($this->_documentType);
    }

    /**
     * Save document to file system and then show download dialog.
     *
     * @param Mage_Sales_Model_Abstract|Mage_Sales_Model_Mysql4_Order_Invoice_Collection|Mage_Sales_Model_Mysql4_Order_Shipment_Collection|Mage_Sales_Model_Mysql4_Order_Creditmemo_Collection $sources
     *
     * @return void
     */
    protected function _saveAndDownloadDocument($sources)
    {
        $fileName = Mage::getModel('advancedinvoicelayout/pdf_file')->getFilenameForSource($sources, $this->_documentType);

        $isSaveToFileEnabled = Mage::getStoreConfigFlag('advancedinvoicelayout/' . $this->_documentType . '/enable_save_to_file');
        if ($isSaveToFileEnabled === true) {
            try {
                $this->_pdf->saveToFile($fileName);
            } catch (Exception $exception) {
                Mage::helper('advancedinvoicelayout')->logError(101, $exception->getMessage(), $exception->getTraceAsString());
                $this->_getSession()->addError('An error occurred while generating ' . $this->_documentType . ' document(s): ' . $exception->getMessage() . ' Check Magento/webserver logs and <a href="http://www.vianetz.com/en/faq" target="_blank">our FAQ</a> for this error.');

                if ($sources instanceof Mage_Sales_Model_Abstract) {
                    $this->_redirect('adminhtml/sales_order_' . $this->_documentType . '/view', array($this->_documentType . '_id' => $sources->getId()));
                } else {
                    $this->_redirect('adminhtml/sales_order_' . $this->_documentType);
                }
                return;
            }
        }

        $this->_prepareDownloadResponse(basename($fileName), $this->_pdf->getContents(), 'application/pdf');
    }

    /**
     * Triggers the mass action for the specified document type.
     *
     * @return Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_AbstractController
     */
    protected function _massOrderAction()
    {
        // We do not use getPost() here because the route is also used for the order detail page button.
        $orderIds = $this->getRequest()->getParam('order_ids');

        if (empty($orderIds) === true) {
            $this->_redirect('adminhtml/sales_order/index');
            return $this;
        }

        if (is_array($orderIds) === false) {
            $orderIds = array($orderIds);
        }

        $sourceCollection = Mage::getResourceModel('sales/order_' . $this->_documentType . '_collection')
            ->addFieldToFilter('order_id', array('in' => $orderIds));
        foreach ($sourceCollection as $sourceModel) {
            $this->_pdf->addSource($sourceModel);
        }

        if ($this->_pdf->countDocuments() === 0) {
            $this->_getSession()->addError($this->__('There are no printable ' . $this->_documentType . 's related to selected orders.'));
            $this->_redirect('adminhtml/sales_order/index');
            return $this;
        }

        $this->_saveAndDownloadDocument($sourceCollection);
        return $this;
    }
}
