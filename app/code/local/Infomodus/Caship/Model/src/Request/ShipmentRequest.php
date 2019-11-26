<?php

/**
 * @author    Danail Kyosev <ddkyosev@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShipmentRequest extends AbstractRequest
{
    protected $required = array(
        'RegionCode' => 'EU',
        'LanguageCode' => 'EN',
        'PiecesEnabled' => 'Y',
        'Billing' => null,
        'Consignee' => null,
        'Dutiable' => null,
        'Reference' => null,
        'ShipmentDetails' => null,
        'Shipper' => null,
        'SpecialService' => null,
        'Notification' => null,
        'LabelImageFormat' => 'PDF',
        'RequestArchiveDoc' => 'Y',
        'Label' => null
    );

    public function setSpecialService(SpecialService $specialService)
    {
        $this->required['SpecialService'] = $specialService;

        return $this;
    }

    protected function buildRoot()
    {
        $root = $this->xml->createElementNS("http://www.dhl.com", 'req:ShipmentRequest');
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.dhl.com ship-val-global-req.xsd'
        );
        $root->setAttribute('schemaVersion', '2.0');

        $this->currentRoot = $this->xml->appendChild($root);

        return $this;
    }

    protected function buildRequestType()
    {
        // No request type for shipment
        return $this;
    }

    /**
     * @param string $regionCode Indicates the shipment to be routed to the specific region eCom backend.
     *                           Valid values are AP, EU and AM.
     */
    public function setRegionCode($regionCode)
    {
        $this->required['RegionCode'] = $regionCode;

        return $this;
    }

    /**
     * @param string $languageCode ISO language code used by the requestor
     */
    public function setLanguageCode($languageCode)
    {
        $this->required['LanguageCode'] = $languageCode;

        return $this;
    }

    /**
     * @param Partials\Billing $billing Billing information of the shipment
     */
    public function setBilling(Billing $billing)
    {
        $this->required['Billing'] = $billing;

        return $this;
    }

    /**
     * @param Partials\Consignee $consignee Shipment receiver information
     */
    public function setConsignee(Consignee $consignee)
    {
        $this->required['Consignee'] = $consignee;

        return $this;
    }

    public function setPlace(Place $place)
    {
        $this->required['Place'] = $place;

        return $this;
    }

    public function setLabel(Label $format)
    {
        $this->required['Label'] = $format;

        return $this;
    }

    public function setReference(Reference $reference)
    {
        $this->required['Reference'] = $reference;

        return $this;
    }

    public function setDutiable(Dutiable $dutiable)
    {
        $this->required['Dutiable'] = $dutiable;

        return $this;
    }

    /**
     * @param Partials\ShipmentDetails $shipmentDetails Shipment details
     */
    public function setShipmentDetails(ShipmentDetails $shipmentDetails)
    {
        $this->required['ShipmentDetails'] = $shipmentDetails;

        return $this;
    }

    /**
     * @param Partials\Shipper $shipper Shipper information
     */
    public function setShipper(Shipper $shipper)
    {
        $this->required['Shipper'] = $shipper;

        return $this;
    }

    public function setNotification(Notification $notification)
    {
        $this->required['Notification'] = $notification;

        return $this;
    }

    public function buildBilling($shipperAccountNumber, $shippingPaymentType, $billingAccountNumber = null)
    {
        $billing = new Billing();
        $billing->setShipperAccountNumber($shipperAccountNumber)
            ->setShippingPaymentType($shippingPaymentType);
        if ($billingAccountNumber && $billingAccountNumber != "T" && $shippingPaymentType == "T") {
            $billing->setBillingAccountNumber($billingAccountNumber);
        }

        return $this->setBilling($billing);
    }

    public function buildConsignee(
        $companyName,
        $addressLine,
        $addressLine2="",
        $addressLine3="",
        $city,
        $postalCode,
        $countryCode,
        $countryName,
        $contactName,
        $contactPhoneNumber,
        $divisionName = null,
        $divisionCode = null
    )
    {
        $consignee = new Consignee();
        $consignee->setCompanyName($companyName)
            ->setAddressLine($addressLine);
        if (strlen($addressLine2) > 0) {
            $consignee->setAddressLine2($addressLine2);
            if (strlen($addressLine3) > 0) {
                $consignee->setAddressLine3($addressLine3);
            }
        }
        $consignee->setCity($city)
            ->setPostalCode($postalCode)
            ->setCountryCode($countryCode)
            ->setCountryName($countryName)
            ->setDivision($divisionName)
            ->setDivisionCode($divisionCode);

        $contact = new Contact();
        $contact->setPersonName($contactName)
            ->setPhoneNumber($contactPhoneNumber);

        $consignee->setContact($contact);

        return $this->setConsignee($consignee);
    }

    public function buildPlace(
        $residenceOrBusiness,
        $companyName,
        $addressLine,
        $city,
        $postalCode,
        $countryCode,
        $countryName,
        $contactName,
        $contactPhoneNumber,
        $division = ""
    )
    {
        $consignee = new Place();
        $consignee->setResidenceOrBusiness($residenceOrBusiness)
            ->setCompanyName($companyName)
            ->setAddressLine($addressLine)
            ->setCity($city)
            ->setPostalCode($postalCode)
            ->setCountryCode($countryCode)
            ->setCountryName($countryName);
        if ($division != "") {
            $consignee->setDivision($division);
        }

        $contact = new Contact();
        $contact->setPersonName($contactName)
            ->setPhoneNumber($contactPhoneNumber);

        $consignee->setContact($contact);

        return $this->setPlace($consignee);
    }

    public function buildShipmentDetails(
        array $pieces,
        $globalProductCode,
        $localProductCode,
        $date,
        $contents,
        $currencyCode,
        $weightUnit = 'K',
        $dimensionUnit = 'C',
        $packageType = null,
        $doorto = null/*,
        $isDutiable = null*/
    )
    {
        $shipmentDetails = new ShipmentDetails();
        $shipmentDetails->setGlobalProductCode($globalProductCode)
            ->setLocalProductCode($localProductCode)
            ->setDate($date)
            ->setContents($contents)
            ->setCurrencyCode($currencyCode)
            ->setWeightUnit($weightUnit)
            ->setDimensionUnit($dimensionUnit)
            ->setPackageType($packageType);

        $pieceId = 0;
        $weight = 0;
        foreach ($pieces as $pieceData) {
            $piece = new ShipmentPiece();
            $piece->setPieceId(++$pieceId);
            if (array_key_exists('height', $pieceData)) {
                $piece->setPackageType($packageType)
                    ->setHeight($pieceData['height'])
                    ->setDepth($pieceData['depth'])
                    ->setWidth($pieceData['width']);
            }

            $piece->setWeight($pieceData['weight']);
            $shipmentDetails->addPiece($piece);
            $weight += (float)$pieceData['weight'];
        }
        $shipmentDetails->setNumberOfPieces($pieceId)
            ->setWeight($weight);
        $shipmentDetails->setDoorTo($doorto);
        /*$shipmentDetails->setIsDutiable($isDutiable);*/

        return $this->setShipmentDetails($shipmentDetails);
    }

    public function buildShipper(
        $shipperId,
        $companyName,
        $addressLine,
        $addressLine2="",
        $addressLine3="",
        $city,
        $postalCode,
        $countryCode,
        $countryName,
        $contactName,
        $contactPhoneNumber,
        $divisionName = null,
        $divisionCode = null
    )
    {
        $shipper = new Shipper();
        $shipper->setShipperId($shipperId)
            ->setCompanyName($companyName)
            ->setAddressLine($addressLine);
        if (strlen($addressLine2) > 0) {
            $shipper->setAddressLine2($addressLine2);
            if (strlen($addressLine3) > 0) {
                $shipper->setAddressLine3($addressLine3);
            }
        }
        $shipper->setCity($city)
            ->setPostalCode($postalCode)
            ->setCountryCode($countryCode)
            ->setCountryName($countryName)
            ->setDivision($divisionName)
            ->setDivisionCode($divisionCode);

        $contact = new Contact();
        $contact->setPersonName($contactName)
            ->setPhoneNumber($contactPhoneNumber);

        $shipper->setContact($contact);

        return $this->setShipper($shipper);
    }

    public function buildNotification($emailAddress, $message)
    {
        $notification = new Notification();
        $notification->setEmailAddress($emailAddress)
            ->setMessage($message);

        return $this->setNotification($notification);
    }

    public function buildLabelFormat($format)
    {
        $label = new Label();
        $label->setLabelTemplate($format);

        return $this->setLabel($label);
    }

    public function buildReference($referenceId)
    {
        $reference = new Reference();
        $reference->setReferenceId($referenceId);

        return $this->setReference($reference);
    }

    public function buildDutiable($declaredValue, $declaredCurrency)
    {
        $dutiable = new Dutiable();
        $dutiable->setDeclaredValue($declaredValue);
        $dutiable->setDeclaredCurrency($declaredCurrency);

        return $this->setDutiable($dutiable);
    }

    public function buildSpecialService($specialType)
    {
        $special = new SpecialService();
        $special->setSpecialServiceType($specialType);

        return $this->setSpecialService($special);
    }
}
