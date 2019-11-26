<?php
 /**
 * GoMage LightCheckout Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2018 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 5.9.4
 * @since        Class available since Release 2.2
 */
	
class GoMage_DeliveryDate_Model_Adminhtml_System_Config_Source_Dateformat{

    const AMERICAN = 0;
    const EUROPEAN = 1;
       
    public function toOptionArray()
    {
        return array(         
            array('value' => self::AMERICAN, 'label'=>'MM.DD.YYYY'),
            array('value' => self::EUROPEAN, 'label'=>'DD.MM.YYYY'),
        );
    }

}