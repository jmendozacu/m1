<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide-v3-0b.htm
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Infomodus_Upstablerates_Model_Carrier_Usps extends Mage_Usa_Model_Shipping_Carrier_Usps
{
	/**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result|bool|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
		$prc	=	Mage::helper('upstablerates')->checkMethodAction();
		if($prc==2){
			if (!$this->getConfigFlag($this->_activeFlag)) {
				echo 1111111; exit;
				return false;
			}

			$this->setRequest($request);

			$this->_result = $this->_getQuotes();

			$this->_updateFreeMethodQuote($request);

			return $this->getResult();
		}else{
			return false;
		}
    }
}
