<?php 
class Infomodus_Upstablerates_Model_Carrier_Freeshipping extends Mage_Shipping_Model_Carrier_Freeshipping{
	/**
     * FreeShipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
		$prc	=	Mage::helper('upstablerates')->checkMethodAction(); 
		if($prc==2){
			return false;
		}
        $result = Mage::getModel('shipping/rate_result');
        $packageValue = $request->getPackageValue();

        $this->_updateFreeMethodQuote($request);

        $allow = ($request->getFreeShipping())
            || ($packageValue >= $this->getConfigData('free_shipping_subtotal'));

        if ($allow) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }
}

?>