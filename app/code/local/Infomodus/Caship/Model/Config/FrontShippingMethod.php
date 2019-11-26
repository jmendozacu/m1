<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Caship_Model_Config_FrontShippingMethod
{
    public function toOptionArray($isMultiSelect = false)
    {
        $storeId = 1;
        

        $option = array();
        $_methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($_methods as $_carrierCode => $_carrier){
            if($_carrierCode !=="dhlint" && $_carrierCode !=="fedex" && $_carrierCode !=="usps" && $_method = $_carrier->getAllowedMethods())  {
                /*if(!$_title = Mage::getStoreConfig('carriers/'.$_carrierCode.'/title')) {*/
                    $_title = $_carrierCode;
                /*}*/
                foreach($_method as $_mcode => $_m){
                    $_code = $_carrierCode . '_' . $_mcode;
                    $option[] = array('label' => "(".$_title.")  ". $_m, 'value' => $_code);
                }
            }
        }
        return $option;
    }
}