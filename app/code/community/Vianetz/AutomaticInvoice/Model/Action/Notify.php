<?php
/**
 * AutomaticInvoice Notify Action Class
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
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
class Vianetz_AutomaticInvoice_Model_Action_Notify extends Vianetz_AutomaticInvoice_Model_Action_Abstract
{
    /**
     * @var string
     */
    const REGISTRY_KEY_NOTIFY_CUSTOMER = 'automaticinvoice_notify_customer';

    /**
     * Check whether the action is allowed to be executed or not.
     *
     * @return boolean
     */
    public function canRun()
    {
        if ($this->sourceModel->getEmailSent() == true) {
            return false;
        }

        return true;
    }

    /**
     * Send mail to configured receivers.
     *
     * @return Vianetz_AutomaticInvoice_Model_Action_Notify
     */
    public function run()
    {
        if ($this->canRun() === false) {
            return $this;
        }

        Mage::helper('automaticinvoice')->log('Trying to send ' . $this->getSourceType() . ' email for order #' . $this->sourceModel->getOrder()->getIncrementId() . ' to store owner/customer..');

        $isSendEmailToCustomer = $this->isSendEmailToCustomer();
        if ($isSendEmailToCustomer === false) {
            Mage::helper('automaticinvoice')->log($this->getSourceType() . ' email will NOT be sent to customer.');
        } else {
            Mage::helper('automaticinvoice')->log($this->getSourceType() . ' email WILL be sent to customer.');
        }

        $this->sourceModel->sendEmail($isSendEmailToCustomer);

        return $this;
    }

    /**
     * Checks whether an email should be send to the customer.
     *
     * This includes 2 checks:
     * 1. check for a registry setting, e.g. this way we can respect the checkbox "Notify customer" if we are in admin environment
     * 2. is email sending configured in the AutomaticInvoice configuration section
     *
     * @return boolean
     */
    protected function isSendEmailToCustomer()
    {
        $isNotifyCustomerRegistryKey = Mage::registry(self::REGISTRY_KEY_NOTIFY_CUSTOMER);
        if (empty($isNotifyCustomerRegistryKey) === false) {
            return $isNotifyCustomerRegistryKey;
        }

        $isSendEmail = Mage::getModel('automaticinvoice/filter_paymentmethodorderstatus')
            ->isPaymentMethodAndOrderStatusActionEnabled('is_notify_customer', $this->sourceModel->getOrder(), $this->getSourceType());
        if ($isSendEmail === false) {
            return false;
        }

        return true;
    }
}