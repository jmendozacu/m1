<?php
class Infomodus_Upstablerates_Model_Carrier_Upstablerates extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'upstablerates';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!$request->getConditionName()) {
            $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);
        }

        $result = Mage::getModel('shipping/rate_result');
        
        $rates = $this->getRate($request);
        //Mage::log(vardump($rates));
        foreach($rates as $rate)
        {
            if (!empty($rate) && $rate['price'] >= 0) 
            {
                $method = Mage::getModel('shipping/rate_result_method');

                $method->setCarrier('upstablerates');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('upsway_' . $rate['pk']);
                $method->setMethodTitle($rate['method_name']);

				$price = $rate['price'];
				
				if ($rate['condition_type'] == 'percent')
				{
					$price = ($price * $request->getData($request->getConditionName())) / 100;
				}
              
                $method->setPrice($this->getFinalPriceWithHandlingFee($price));
                $method->setCost($rate['cost']);
    
                $result->append($method);
            }            
        }

        return $result;
	}
	
	public function getRate(Mage_Shipping_Model_Rate_Request $request)
	{
		return Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates')->getRate($request);
	}	

	public function getCode($type, $code='')
    {
        $codes = array(

            'condition_name'=>array(
                'package_weight' => Mage::helper('shipping')->__('Weight vs. Destination'),
                'package_value'  => Mage::helper('shipping')->__('Price vs. Destination'),
                'package_qty'    => Mage::helper('shipping')->__('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => Mage::helper('shipping')->__('Weight (and above)'),
                'package_value'  => Mage::helper('shipping')->__('Order Subtotal (and above)'),
                'package_qty'    => Mage::helper('shipping')->__('# of Items (and above)'),
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tablerate Rate code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tablerate Rate code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('upsway'=>$this->getConfigData('name'));
    }
}