<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Adminhtml_UpslabelinvController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('upslabelinv/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function showlabelAction() {
        $order_id = $this->getRequest()->getParam('order_id');
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_label_tabs'));
        $this->renderLayout();
    }
    public function intermediateAction() {
        $order_id = $this->getRequest()->getParam('order_id');
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_label_intermediate'));
        $this->renderLayout();
    }

    public function deletelabelAction() {
        $order_id = $this->getRequest()->getParam('order_id');
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_label_del'));
        $this->renderLayout();
    }

    public function printAction() {
        $imname = $this->getRequest()->getParam('imname');
$path = Mage::getBaseDir('media') . DS . 'upslabelinv' . DS.'label'. DS;
$path_url = Mage::getBaseUrl('media') . DS . 'upslabelinv' . DS.'label'. DS;
        $imageVerticalNameUrl = $path_url.$imname;
        if(Mage::getStoreConfig('upslabelinv/labeloptions/verticalprint')==1){
            $imageVerticalName = 'vertical/vertical'.str_replace('.gif','.jpg',$imname);
            $imageVerticalNamePath = $path.$imageVerticalName;
            $imageVerticalNameUrl = $path_url.$imageVerticalName;
            if(!file_exists($imageVerticalNamePath)){
                if(!is_dir($path.'vertical')){
                    mkdir($path.'vertical' . DS, 0777);
                }
                $f_cont = file_get_contents($path_url.$imname);
                $img=imagecreatefromstring($f_cont);
                $FullImage_width = imagesx ($img);
                $FullImage_height = imagesy ($img);
                $full_id = imagecreatetruecolor($FullImage_width, $FullImage_height);
                $col = imagecolorallocate($img, 125, 174, 240);
                $full_id = imagerotate($img, -90, $col);
                imagejpeg($full_id, $imageVerticalNamePath);
            }
        }

        echo '<html>
<head>
<title>Print Shipping Label</title>
</head>
<body>
<style>
img {
'.(Mage::getStoreConfig('upslabelinv/labeloptions/verticalprint')==1?'height:100%;':'width:100%;').'
}
</style>
<img src="'.$imageVerticalNameUrl.'" />
<script>
window.onload = function(){window.print();}
</script>
</body>
</html>';
        exit;
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('upslabelinv/upslabelinv')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('upslabelinv_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('upslabelinv/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_edit'))
                    ->_addLeft($this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabelinv')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('upslabelinv/upslabelinv');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('upslabelinv')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabelinv')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('upslabelinv/upslabelinv');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $upslabelIds = $this->getRequest()->getParam('upslabelinv');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getModel('upslabelinv/upslabelinv')->load($upslabelId);
                    $upslabel->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($upslabelIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $upslabelIds = $this->getRequest()->getParam('upslabelinv');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getSingleton('upslabelinv/upslabelinv')
                                    ->load($upslabelId)
                                    ->setStatus($this->getRequest()->getParam('status'))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($upslabelIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'upslabelinv.csv';
        $content = $this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_grid')
                        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'upslabelinv.xml';
        $content = $this->getLayout()->createBlock('upslabelinv/adminhtml_upslabelinv_grid')
                        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
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
        die;
    }

}