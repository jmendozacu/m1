<?php

class Vianetz_AutomaticInvoice_Model_Queue_Message_Generate extends Vianetz_AsyncQueue_Model_Message implements Vianetz_AsyncQueue_Model_MessageInterface
{
    /**
     * @return boolean
     */
    public function validate()
    {
        $orderId = $this->getOrderId();
        $sourceType = $this->getSourceType();

        if (empty($orderId) === true || empty($sourceType) === true) {
            Mage::helper('automaticinvoice')->log('Incorrect queue message format. Skipping.', LOG_ERR);
            return false;
        }

        return true;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            /** @var \Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($this->getOrderId());

            if (empty($order) === true) {
                Mage::helper('automaticinvoice')->log('Order with id ' . $this->getOrderId() . ' cannot be loaded.', LOG_ERR);
                return $this;
            }

            /** @var Vianetz_AutomaticInvoice_Model_Action_Abstract $createAction */
            $createAction = Mage::getModel('automaticinvoice/action_' . $this->getSourceType() . '_create');
            $createAction->setOrder($order)
                ->run();

            if ($this->getIsNotifyCustomer() === true) {
                Mage::unregister(Vianetz_AutomaticInvoice_Model_Action_Notify::REGISTRY_KEY_NOTIFY_CUSTOMER);
                Mage::register(Vianetz_AutomaticInvoice_Model_Action_Notify::REGISTRY_KEY_NOTIFY_CUSTOMER, true);
            }

            if ($this->getIsSendEmail() === true) {
                Mage::getModel('automaticinvoice/action_notify')
                    ->setSourceModel($createAction->getResult())
                    ->run();
            }
        } catch (Exception $exception) {
            Mage::helper('automaticinvoice')->log('Order with id ' . $this->getOrderId() . ' cannot be processed: ' . $exception->getMessage(), LOG_ERR);
        }

        return $this;
    }

    /**
     * @param integer $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->messageData['orderId'] = $orderId;
        return $this;
    }

    /**
     * @param string $sourceType
     *
     * @return $this
     */
    public function setSourceType($sourceType)
    {
        $this->messageData['sourceType'] = $sourceType;
        return $this;
    }

    /**
     * @param bool $isSendEmail
     *
     * @return $this
     */
    public function setIsSendEmail($isSendEmail = true)
    {
        $this->messageData['isSendEmail'] = $isSendEmail;
        return $this;
    }

    /**
     * @param bool $isNotifyCustomer
     *
     * @return $this
     */
    public function setIsNotifyCustomer($isNotifyCustomer = true)
    {
        $this->messageData['isNotifyCustomer'] = $isNotifyCustomer;
        return $this;
    }

    /**
     * @return null|integer
     */
    private function getOrderId()
    {
        $parameters = $this->getParameters();

        if (isset($parameters['orderId']) === false) {
            return null;
        }

        return (int)$parameters['orderId'];
    }

    /**
     * @return null|string
     */
    private function getSourceType()
    {
        $parameters = $this->getParameters();

        if (isset($parameters['sourceType']) === false) {
            return null;
        }

        return $parameters['sourceType'];
    }

    /**
     * @return boolean
     */
    private function getIsSendEmail()
    {
        $parameters = $this->getParameters();

        if (isset($parameters['isSendEmail']) === false) {
            return false;
        }

        return $parameters['isSendEmail'];
    }

    /**
     * @return boolean
     */
    private function getIsNotifyCustomer()
    {
        $parameters = $this->getParameters();

        if (isset($parameters['isNotifyCustomer']) === false) {
            return false;
        }

        return $parameters['isNotifyCustomer'];
    }
}