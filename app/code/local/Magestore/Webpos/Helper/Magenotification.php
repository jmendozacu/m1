<?php

class Magestore_Webpos_Helper_Magenotification extends Magestore_Magenotification_Helper_Data
{
    public function checkLicenseKey($extensionName)
    {
        if (strpos($extensionName, 'Magestore') === 0) {
            $arrName = explode('_', $extensionName);
            $extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore', '', $extensionName);
        }
        if ($extensionName == 'Webpos') {
            $extensionName = 'Onestepcheckout';
        }
        return parent::checkLicenseKey($extensionName);
    }
    
    public function getLicenseInfo($licensekey, $extensionName)
    {
        if (strpos($extensionName, 'Magestore') === 0) {
            $arrName = explode('_', $extensionName);
            $extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore', '', $extensionName);
        }
        if ($extensionName == 'Webpos') {
            $extensionName = 'Onestepcheckout';
        }
        return parent::getLicenseInfo($licensekey, $extensionName);
    }
    
    public function getLicenseType($extensionName)
    {
        if (strpos($extensionName, 'Magestore') === 0) {
            $arrName = explode('_', $extensionName);
            $extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore', '', $extensionName);
        }
        if ($extensionName == 'Webpos') {
            $extensionName = 'Onestepcheckout';
        }
        return parent::getLicenseType($extensionName);
    }
}

?>