<?php
class AD_CashPayments_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'cashpayments';

    public function __construct() {
        // Disable payment method for the front-end
        if(Mage::getStoreConfig('payment/cashpayments/backend_only') == 1) {
            $this->_canUseCheckout = false;
            $this->_canUseForMultishipping = false;
        }

        parent::__construct();
    }

    public function assignData($data) {
        $details = array();
        if ($this->getPayableTo()) {
            $details['payable_to'] = $this->getPayableTo();
        }

        if ($this->getMailingAddress()) {
            $details['mailing_address'] = $this->getMailingAddress();
        }

        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }

        return $this;
    }

    public function getPayableTo() {
        return $this->getConfigData('payable_to');
    }

    public function getMailingAddress() {
        return $this->getConfigData('mailing_address');
    }
}

