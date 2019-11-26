<?php

/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Label_Del extends Mage_Adminhtml_Block_Widget_Tabs {

    protected function _beforeToHtml() {
        $AccessLicenseNumber = Mage::getStoreConfig('upslabelinv/profile/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabelinv/profile/userid');
        $Password = Mage::getStoreConfig('upslabelinv/profile/password');
        $shipperNumber = Mage::getStoreConfig('upslabelinv/profile/shippernumber');

        $path = Mage::getBaseDir('media') . DS . 'upslabelinv' . DS;

        $lbl = new Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Ups();

        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
        $lbl->packagingReferenceNumberCode = Mage::getStoreConfig('upslabelinv/profile/packagingreferencenumbercode');

        $order_id = $this->getRequest()->getParam('order_id');
        $direction = $this->getRequest()->getParam('direction');

        $collection = Mage::getModel('upslabelinv/upslabelinv');
        $coll1 = $collection->getCollection()->addFieldToFilter('type', $direction)->addFieldToFilter('order_id', $order_id);
        $coll = $coll1->getData();
        $coll = $coll[0];
        $result = $lbl->deleteLabel($coll['shipmentidentificationnumber']);
        /*if (!is_array($result)) {*/
            @unlink(Mage::getBaseDir('media') . '/upslabelinv/label/' . $coll['labelname']);
            @unlink(Mage::getBaseDir('media') . '/upslabelinv/label/' . $coll['trackingnumber'].'.html');
            @unlink(Mage::getBaseDir('media') . '/upslabelinv/label/' . "HVR".$coll['trackingnumber'].".html");
            $collection->setId($coll['upslabelinv_id'])->delete();
            echo '<br />Removal was successful. Back to <a href="' . $this->getUrl('adminhtml/sales_order/view/order_id/' . $order_id) . '">order</a>';
        /*} else {
            echo 'Error';
            print_r($result);
        }*/


        return parent::_beforeToHtml();
    }

}