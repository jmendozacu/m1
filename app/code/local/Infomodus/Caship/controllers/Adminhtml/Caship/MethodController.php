<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Adminhtml_Caship_MethodController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('caship/method')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('method_data', $model);

            $this->loadLayout();
            /*$this->_setActiveMenu('caship/conformity');*/

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Infomodus Shipping Method Manager'), Mage::helper('adminhtml')->__('Infomodus Shipping Method Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Infomodus Shipping Method News'), Mage::helper('adminhtml')->__('Infomodus Shipping Method News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('caship/adminhtml_method_edit'))
                ->_addLeft($this->getLayout()->createBlock('caship/adminhtml_method_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('caship')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('caship/method');

            if (isset($data['country_ids']) && !empty($data['country_ids'])) {
                $data['country_ids'] = implode(",", $data['country_ids']);
            } else {
                $data['country_ids'] = '';
            }

            if (isset($data['user_group_ids']) && !empty($data['user_group_ids'])) {
                $data['user_group_ids'] = implode(",", $data['user_group_ids']);
            } else {
                $data['user_group_ids'] = '';
            }

            if (isset($data['product_categories']) && !empty($data['product_categories'])) {
                $data['product_categories'] = ",".implode(",", $data['product_categories']).",";
            } else {
                $data['product_categories'] = '';
            }

            if (isset($data['dinamic_price']) && $data['dinamic_price'] == 1 && isset($data['company_type_all'])) {
                $data['company_type'] = $data['company_type_all'];
                switch ($data['company_type_all']) {
                    case 'ups':
                    case 'upsinfomodus':
                        $data['upsmethod_id'] = $data['upsmethod_id_all'];
                        break;
                    case 'dhl':
                    case 'dhlinfomodus':
                        $data['dhlmethod_id'] = $data['dhlmethod_id_all'];
                        break;
                }
            }
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if (!isset($response['error'])) {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                    return;
                } else {
                    echo $response['error'];
                    exit;
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('caship')->__('Unable to find method to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            try {
                $collection = Mage::getModel('caship/method')->load($id);
                $collection->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $cashipIds = $this->getRequest()->getParam('method');
        if (!is_array($cashipIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($cashipIds as $cashipId) {
                    $collection = Mage::getModel('caship/method')->load($cashipId);
                    $collection->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($cashipIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'infomodus_shipping_methods.csv';
        $content = $this->getLayout()->createBlock('caship/adminhtml_method_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportAction()
    {
        $fileName = 'infomodus_shipping_methods.csv';
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fp = fopen('php://output', 'w');
        $collection = Mage::getModel('caship/method')->getCollection();
        $firstRow = array(
            'title',
            'name',
            'description',
            'dynamic_price',
            'price',
            'added_price_type',
            'added_price_value',
            'carrier_code',
            'carrier_method_code',
            'carrier_method_code2',
            'carrier_method_code3',
            'negotiated',
            'negotiated_amount_from',
            'order_amount_min',
            'order_amount_max',
            'weight_min',
            'weight_max',
            'qty_min',
            'qty_max',
            'zip_min',
            'zip_max',
            'tax',
            'country_ids',
            'user_group_ids',
            'product_categories',
            'store_code',
            'sort',
            'status',
            /*'weight_jump_for_price_doubling',
            'weight_jump_for_new_package',*/
        );
        fputcsv($fp, $firstRow, ',');
        $manager = Mage::getModel('core/store');
        foreach ($collection AS $item) {
            $itemData = $item->getData();
            $row = array();
            $row[] = $itemData['title'];
            $row[] = $itemData['name'];
            $row[] = $itemData['description'];
            $row[] = $itemData['dinamic_price'];
            $row[] = $itemData['price'];
            $row[] = $itemData['added_value_type'];
            $row[] = $itemData['added_value'];
            $row[] = $itemData['company_type'];
            if ($itemData['company_type'] == 'ups' || $itemData['company_type'] == 'upsinfomodus') {
                $row[] = $itemData['upsmethod_id'];
            }  else {
                $row[] = '';
            }
            if ($itemData['company_type'] == 'ups' || $itemData['company_type'] == 'upsinfomodus') {
                $row[] = $itemData['upsmethod_id_2'];
            }  else {
                $row[] = '';
            }
            if ($itemData['company_type'] == 'ups' || $itemData['company_type'] == 'upsinfomodus') {
                $row[] = $itemData['upsmethod_id_3'];
            }  else {
                $row[] = '';
            }
            $row[] = $itemData['negotiated'];
            $row[] = $itemData['negotiated_amount_from'];
            $row[] = $itemData['amount_min'];
            $row[] = $itemData['amount_max'];
            $row[] = $itemData['weight_min'];
            $row[] = $itemData['weight_max'];
            $row[] = $itemData['qty_min'];
            $row[] = $itemData['qty_max'];
            $row[] = $itemData['zip_min'];
            $row[] = $itemData['zip_max'];
            $row[] = $itemData['tax'];
            if ($itemData['is_country_all'] == 0) {
                $row[] = 'all';
            } else {
                $row[] = $itemData['country_ids'];
            }
            $row[] = $itemData['user_group_ids'];
            $row[] = $itemData['product_categories'];
            $store = $manager->load($itemData['store_id']);
            $row[] = $store->getCode();
            $row[] = $itemData['sort'];
            $row[] = $itemData['status'];
            /*$row[] = $itemData['increment_price_by_weight'];
            $row[] = $itemData['increment_package_by_weight'];*/
            fputcsv($fp, $row, ',');
        }
        fclose($fp);
        exit;
    }

    public function exportXmlAction()
    {
        $fileName = 'infomodus_shipping_methods.xml';
        $content = $this->getLayout()->createBlock('caship/adminhtml_method_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        return;
    }
}