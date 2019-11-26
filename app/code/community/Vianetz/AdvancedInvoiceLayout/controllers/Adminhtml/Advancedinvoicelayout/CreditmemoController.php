<?php
/**
 * AdvancedInvoiceLayout creditmemo controller class
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
require_once(Mage::getModuleDir('controllers', 'Vianetz_AdvancedInvoiceLayout') . DS . 'Adminhtml' . DS . 'Advancedinvoicelayout' . DS . 'AbstractController.php');
class Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_CreditmemoController extends Vianetz_AdvancedInvoiceLayout_Adminhtml_Advancedinvoicelayout_AbstractController
{
    /**
     * @var string
     */
    protected $_documentType = Vianetz_AdvancedInvoiceLayout_Model_Source_Type::SOURCE_TYPE_CREDITMEMO;

    /**
     * Check if module is active (System->Configuration->Advanced) & System->Configuration->AdvancedInvoiceLayout->Invoice/Shipment/Creditmemo.
     *
     * @return Mage_Adminhtml_Controller_Action|void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (Mage::helper('advancedinvoicelayout')->isModuleActive($this->_documentType) === false) {
            // We print the default template if module is not active.
            $this->_forward('adminhtml/order_creditmemo/print', array('_current' => true));
        }
    }

    /**
     * Print PDF creditmemo.
     *
     * @return void
     */
    public function printpdfAction()
    {
        $creditmemoId = (int)$this->getRequest()->getParam('creditmemo_id');
        if (empty($creditmemoId) === true) {
            $this->_forward('noRoute');
        }

        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
        if (empty($creditmemo) === true) {
            $this->_forward('noRoute');
        }

        $this->_pdf->addSource($creditmemo);

        $this->_saveAndDownloadDocument($creditmemo);
    }

    /**
     * Overwrites original pdfcreditmemos action to support the configured file name in AIL settings.
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfcreditmemosAction()
    {
        $creditmemoIds = $this->getRequest()->getPost('creditmemo_ids');
        if (empty($creditmemoIds) === true) {
            $this->_redirect('*/*/');
        }

        /** @var $creditmemos Mage_Sales_Model_Mysql4_Order_Creditmemo_Collection */
        $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $creditmemoIds))
            ->load();
        foreach ($creditmemos as $creditmemo) {
            $this->_pdf->addSource($creditmemo);
        }

        $this->_saveAndDownloadDocument($creditmemos);
    }

    /**
     * Overwrites original pdfcreditmemos action to support the configured file name in AIL settings.
     *
     * @see Mage_Adminhtml_Sales_OrderController::pdfcreditmemosAction()
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfcreditmemosbyorderAction()
    {
        return $this->_massOrderAction();
    }
}
