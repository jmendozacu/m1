<?php
class Infomodus_Upslabelinv_Block_Label_View extends Mage_Core_Block_Template
{
    protected $_order;
    protected $_labelCollection;

    public function __construct(){
        parent::_construct();
    }

    public function getOrder() {

        if(is_null($this->_order)) {
            $order_id = $this->getOrderId();
            if($order_id) {
                return Mage::getModel('sales/order')->load($order_id);
            } else {
                return false;
            }
        }

        return $this->_order;
    }

    public function getOrderId() {

        if($this->getRequest()->getParam('order_id')) {
            return $this->getRequest()->getParam('order_id');
        } else if($this->getOrder()) {
            return $this->getOrder()->getId();
        } else {
            return false;
        }

    }

    public function canViewLabel() {

        if($this->getOrder()->getCustomerId() == Mage::getSingleton('customer/session')->getId()) {
            // check if order's customer ID matches current logged in session's customer ID
            return true;
        } else if(Mage::getStoreConfig('upslabelinv/access/guest')==1) {
            // check if module config allows for guests to view labels
            return true;
        } else {
            // if all else fails then return false
            return false;
        }

    }

    public function getOrderLabel() {

        if(is_null($this->_labelCollection)) {
            $this->_labelCollection = Mage::getModel('upslabelinv/upslabelinv')->getCollection()
                        ->addFieldToFilter('type', 'from')
                        ->addFieldToFilter('order_id', $this->getOrderId())
                        ->setOrder('upslabelinv_id','desc');
        }

        $labelCollection = $this->_labelCollection;

        if (!$labelCollection->count()) {

            // if for some reason no label has been returned in collection,
            // but an Order ID has been supplied -
            // then (re)generate the label. (it is likely a PayPal order)
            if($this->getOrderId() !== false) {

				try {
					// do same logic as Infomodus_Upslabelinv_Checkout_Model_Type_Onepage_Save_Order_Observer
					// which runs after checkout_type_onepage_save_order_after (not triggered by PayPal orders)
					$upslabel = new Infomodus_Upslabelinv_Block_Adminhtml_Upslabelinv_Label_Tabs();

					$shipMethodArray = explode('_', $this->getOrder()->getShippingMethod());
					$shipWay = 0;
					if ($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) {
						$upstablerates = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->loadPk($shipMethodArray[2]);
						$shipWay = $upstablerates['way'];
					} elseif ($shipMethodArray[0] == 'caship') {
                        $upsmethodId = $shipMethodArray[1];
                        $cashipModel = Mage::getModel('caship/method')->load($upsmethodId);
                        if ($cashipModel) {
                            $shipWay = $cashipModel->getDirectionType();
                        } else {
                            return false;
                        }
                    } else {
						return false;
					}
                    if((int)$shipWay!=1) {
                        if ((int)$shipWay == 3) {
                            $upslabel->createLabel($this->getOrder()->getId(), 'to', true);
                        }
                        if (($shipMethodArray[0] == 'upstablerates' && count($shipMethodArray) > 2) || $shipMethodArray[0] == 'caship') {
                            $upslabel->createLabel($this->getOrder()->getId(), 'from', true);
                        }

                        // now that the label has been (re)generated,
                        // reset the label collection and recall this function
                        // to check if the label now exists.
                        $this->_labelCollection = null;
                        return $this->getOrderLabel();
                    } else {
                        return false;
                    }
					
				} catch(Exception $e) {
					Mage::log('');
					Mage::log($e->getMessage());
					Mage::log('');
					return false;
				}

            }

            return false;
        } else {

            // return first label found in collection
            return $labelCollection->getFirstItem();

        }

    }
}
