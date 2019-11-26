<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Connection/DHLHttpConnection.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Exception/DHLConnectionException.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/AbstractRequest.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/GetQuoteRequest.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/RequestPartial.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Billing.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/BkgDetails.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Consignee.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Contact.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Location.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Notification.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Piece.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/PieceRate.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Place.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/ShipmentDetails.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/ShipmentPiece.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Shipper.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Label.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Dutiable.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/DutiableQuot.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/Reference.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/Partials/SpecialService.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Request/ShipmentRequest.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Response/GetQuoteResponse.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Response/Partials/Price.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/Response/ShipmentResponse.php");
require_once(Mage::getBaseDir('app') . "/code/local/Infomodus/Caship/Model/src/XMLSerializer.php");

class Infomodus_Caship_Model_Dhl
{
    public $packages;
    public $weightUnits;
    public $packageWeight;

    public $packageType;

    public $UserId;
    public $Password;
    public $shipperNumber;

    public $shipperCity;
    public $shipperStateProvinceCode;
    public $shipperPostalCode;
    public $shipperCountryCode;

    public $shiptoCity;
    public $shiptoStateProvinceCode;
    public $shiptoPostalCode;
    public $shiptoCountryCode;

    public $declaredValue;

    public $currencyCode;

    public $testing;

    function getShipPrice($isDutiable = false)
    {
        $request = new GetQuoteRequest($this->UserId, $this->Password);

        $destWeightUnit = Mage::helper('caship')->getWeightUnitByCountry($this->shiptoCountryCode);
        $weingUnitKoef = 1;
        switch ($destWeightUnit) {
            case 'KG':
                $destWeightUnit = 'K';
                break;
            case 'LB':
                $destWeightUnit = 'L';
                break;
        }
        if ($destWeightUnit != $this->weightUnits) {
            if ($destWeightUnit == 'L') {
                $weingUnitKoef = 2.2046;
            } else {
                $weingUnitKoef = 1 / 2.2046;
            }
        }

        $destDimentionUnit = Mage::helper('caship')->getDimensionUnitByCountry($this->shiptoCountryCode);
        switch ($destDimentionUnit) {
            case 'CM':
                $destDimentionUnit = 'C';
                break;
            case 'IN':
                $destDimentionUnit = 'I';
                break;
        }
        $dimentionUnitKoef = 1;
        if ($destWeightUnit != $this->weightUnits) {
            if ($destWeightUnit == 'C') {
                $dimentionUnitKoef = 2.54;
            } else {
                $dimentionUnitKoef = 1 / 2.54;
            }
        }

        /* Multipackages */
        $pieces = array();
        foreach ($this->packages AS $pv) {
            $pieces1 = array();
            $packweight = array_key_exists('packweight', $pv) ? (float)str_replace(',', '.', $pv['packweight']) * $weingUnitKoef : 0;
            $weight = array_key_exists('weight', $pv) ? (float)str_replace(',', '.', $pv['weight']) * $weingUnitKoef : 0;
            $pieces1['weight'] = round(($weight + (is_numeric($packweight) ? $packweight : 0)), 3);
            $pieces[] = $pieces1;
        }
        /* END Multipackages */

        $request->buildFrom($this->shipperCountryCode, $this->shipperPostalCode, $this->shipperCity)
            ->buildBkgDetails($this->shipperCountryCode, new DateTime('now'),
                $pieces, 'PT10H21M', ($destDimentionUnit == 'C' ? 'CM' : 'IN'), ($destWeightUnit == 'K' ? 'KG' : 'LB'), $isDutiable, $this->shipperNumber)
            ->buildTo($this->shiptoCountryCode, $this->shiptoPostalCode, $this->shiptoCity);
        if ($isDutiable) {
            $request->buildDutiable($this->declaredValue, $this->currencyCode);
        }
        $requestData = $request->send($this->testing);
        /*Mage::log("DHL test mode ".$this->testing);*/
        /*$path_xml = Mage::getBaseDir('media') . DS . 'dhllabel' . DS . "test_xml" . DS;
        $dopName = '';
        if($isDutiable == true){
            $dopName = 'withDutiable';
        }
        file_put_contents($path_xml . "CapabilityRequest".$dopName.".xml", $request->responce);

        file_put_contents($path_xml . "CapabilityResponse".$dopName.".xml", $requestData);
*/
        if (Mage::getStoreConfig('carriers/caship/debug') == 1) {
            Mage::log($requestData, null, 'caship_debug.log');
            Mage::log($request->responce, null, 'caship_debug.log');
        }
        $response = new GetQuoteResponse($requestData);
        return $response->getPrices();
    }
}