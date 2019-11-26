<?php

class EmjaInteractive_PurchaseorderManagement_Adminhtml_Po_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function printAction()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')) {
            if ($order = Mage::getModel('sales/order')->load($orderId)) {
                $pdf = Mage::getModel('emjainteractive_purchaseordermanagement/sales_order_pdf')->getPdf(array($order));
                return $this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            }
        } else {
            $this->_forward('noRoute');
        }
    }

    public function pdfordersAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        $pdf = null;
        if (!empty($orderIds)) {
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToFilter('entity_id', $orderIds)
                ->load();

            if (count($orders)) {
                $flag = true;
                $pdf = Mage::getModel('emjainteractive_purchaseordermanagement/sales_order_pdf')->getPdf($orders);
            }

            if ($flag) {
                return $this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/sales_order/');
            }
        }

        $this->_redirect('*/sales_order/');
    }
	
	public function savePaymentAction()
    {
        $purchaseOrder = $this->getRequest()->getParam('purchaseorder', null);
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order || !$order->getId()) {
                Mage::throwException('Could not find order for update.');
            }

            if (empty($purchaseOrder) || empty($purchaseOrder['number'])) {
                Mage::throwException('Purchase Order Number should not be empty.');
            }

            $payment = $order->getPayment();
            $originalPoNumber = $payment->getPoNumber();
            $payment->setPoNumber($purchaseOrder['number']);
            $order->setPOUpdated(true);
            $order->addStatusHistoryComment(sprintf(
                'Purchase Order Number changed by "%s" from "%s" to "%s".',
                Mage::getSingleton('admin/session')->getUser()->getUsername(),
                Mage::helper('core')->escapeHtml($originalPoNumber),
                Mage::helper('core')->escapeHtml($purchaseOrder['number'])
            ));

            /** @var Mage_Core_Model_Resource_Transaction $transaction */
            $transaction = Mage::getModel('core/resource_transaction');
            $transaction->addObject($order);
            $transaction->addObject($payment);
            $transaction->save();

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError('Could not save PurchaseOIrder number.');
        }

        return $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
    }

    protected function _isAllowed()
    {
        return true;
    }
}
