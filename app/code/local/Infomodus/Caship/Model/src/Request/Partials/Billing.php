<?php
class Billing extends RequestPartial
{
    protected $required = array(
        'ShipperAccountNumber' => null,
        'ShippingPaymentType' => 'S',
        'BillingAccountNumber' => null
    );

    /**
     * @param string $shipperAccountNumber DHL account number of the shipper
     */
    public function setShipperAccountNumber($shipperAccountNumber)
    {
        $this->required['ShipperAccountNumber'] = $shipperAccountNumber;

        return $this;
    }

    /**
     * @param string $shippingPaymentType Method of payment
     *                                    Valid values are S(Shipper), R(Recipient), T(Third Party/Other)
     */
    public function setShippingPaymentType($shippingPaymentType)
    {
        $this->required['ShippingPaymentType'] = $shippingPaymentType;

        return $this;
    }

    public function setBillingAccountNumber($billingAccountNumber)
    {
        $this->required['BillingAccountNumber'] = $billingAccountNumber;

        return $this;
    }
}
