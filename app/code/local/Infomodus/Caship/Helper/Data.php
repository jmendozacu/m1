<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $error = true;
    public $testing = false;
    private $ch;

    public function toOptionArrayStores()
    {
        $c = array(Mage::helper('caship')->__("All Stores"));
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    //print_r($store->getId()); exit;
                    $c[] = array('label' => $store->getName()." (".$website->getName()." \\ ".$group->getName().")", 'value' => $store->getId());
                }
            }
        }

        return $c;
    }

    public function getStores()
    {
        $c = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $c[$store->getId()] = $store->getName()." (".$website->getName()." \\ ".$group->getName().")";
                }
            }
        }
        return $c;
    }

    static public function escapeXML($string)
    {
        $string = preg_replace('/&/is', '&amp;', $string);
        $string = preg_replace('/</is', '&lt;', $string);
        $string = preg_replace('/>/is', '&gt;', $string);
        $string = preg_replace('/\'/is', '&#39;', $string);
        $string = preg_replace('/"/is', '&quot;', $string);
        $string = str_replace(array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż', 'ü', 'ò', 'è', 'à', 'ì', 'é', 'ô'), array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z', 'u', 'o', 'e', 'a', 'i', 'e', 'o'), $string);
        return mb_encode_numericentity(trim($string), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }

    public function curlSend($url, $data = NULL)
    {
        $this->error = true;
        $result = $this->curlSetOption($url, $data);
        $ch = $this->ch;
        if ($result) {
            $result1 = $result;
            $result = strstr($result, '<?xml');
            if ($result === FALSE) {
                $result = $result1;
            }
            curl_close($ch);
            $this->error = false;
            return $result;
        } else {
            $error = '<h1>Error</h1> <ul>';
            $error .= '<li>Error Severity : Hard</li>';
            $error .= '<li>Error Description : ' . curl_errno($ch) . ' - ' . curl_error($ch) . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . curl_errno($ch) . ' - ' . curl_error($ch) . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            curl_close($ch);
            $this->error = true;
            return array('errordesc' => 'Server Error (cUrl): '. curl_errno($ch) . ' - ' . curl_error($ch), 'error' => $error);
        }
    }

    public function curlSetOption($url, $data = NULL)
    {
        /*$sslV = curl_version();*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        /*if ($data != NULL) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {*/
            curl_setopt($ch, CURLOPT_HEADER, 0);
        /*}*/
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->testing);
        /*if (strpos($sslV['ssl_version'], 'NSS/') === FALSE) {
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
        }*/
        if ($data !== NULL) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $this->ch = $ch;
        return curl_exec($ch);
    }

    

    public function getUpsCode($code){
        $codes = array(
            '1DM' => '14',
            '1DA' => '01',
            '1DP' => '13',
            '2DM' => '59',
            '2DA' => '02',
            '3DS' => '12',
            'GND' => '03',
            'EP' => '54',
            'XDM' => '54',
            'XPD' => '08',
            'XPR' => '07',
            'ES' => '07',
            'SV' => '65',
            'EX' => '08',
            'ST' => '11',
            'ND' => '07',
            'WXS' => '65',
            '21' => '54',
            '01' => '07',
            '05' => '08',
            '08' => '11',
            '18' => array('86', '65'),
            '10' => array('85', '07'),
            '22' => '85',

        );
        return isset($codes[$code])?$codes[$code]:null;
    }

    public function getWeightUnitByCountry($countryCode){
        $c = array(
            'AD' => 'KG',
            'AE' => 'KG',
            'AF' => 'LB',
            'AG' => 'KG',
            'AI' => 'KG',
            'AL' => 'KG',
            'AM' => 'KG',
            'AN' => 'KG',
            'AO' => 'LB',
            'AR' => 'KG',
            'AS' => 'KG',
            'AT' => 'LB',
            'AU' => 'KG',
            'AW' => 'KG',
            'AZ' => 'LB',
            'BA' => 'KG',
            'BB' => 'KG',
            'BD' => 'KG',
            'BE' => 'KG',
            'BF' => 'KG',
            'BG' => 'KG',
            'BH' => 'KG',
            'BI' => 'LB',
            'BJ' => 'KG',
            'BM' => 'KG',
            'BN' => 'LB',
            'BO' => 'KG',
            'BR' => 'KG',
            'BS' => 'KG',
            'BT' => 'LB',
            'BW' => 'KG',
            'BY' => 'KG',
            'BZ' => 'KG',
            'CA' => 'KG',
            'CD' => 'KG',
            'CF' => 'KG',
            'CG' => 'KG',
            'CH' => 'KG',
            'CI' => 'KG',
            'CK' => 'KG',
            'CL' => 'KG',
            'CM' => 'KG',
            'CN' => 'KG',
            'CO' => 'KG',
            'CR' => 'LB',
            'CU' => 'LB',
            'CV' => 'KG',
            'CY' => 'KG',
            'CZ' => 'KG',
            'DE' => 'KG',
            'DJ' => 'KG',
            'DK' => 'KG',
            'DM' => 'LB',
            'DO' => 'KG',
            'DZ' => 'KG',
            'EC' => 'KG',
            'EE' => 'LB',
            'EG' => 'KG',
            'ER' => 'KG',
            'ES' => 'KG',
            'ET' => 'KG',
            'FI' => 'KG',
            'FJ' => 'KG',
            'FK' => 'KG',
            'FM' => 'KG',
            'FO' => 'KG',
            'FR' => 'KG',
            'GA' => 'LB',
            'GB' => 'KG',
            'GD' => 'LB',
            'GE' => 'KG',
            'GF' => 'KG',
            'GG' => 'KG',
            'GH' => 'LB',
            'GI' => 'KG',
            'GL' => 'KG',
            'GM' => 'KG',
            'GN' => 'KG',
            'GP' => 'KG',
            'GQ' => 'KG',
            'GR' => 'KG',
            'GT' => 'KG',
            'GU' => 'KG',
            'GW' => 'KG',
            'GY' => 'KG',
            'HK' => 'KG',
            'HN' => 'KG',
            'HR' => 'LB',
            'HT' => 'KG',
            'HU' => 'KG',
            'IC' => 'LB',
            'ID' => 'KG',
            'IE' => 'KG',
            'IL' => 'LB',
            'IN' => 'KG',
            'IQ' => 'KG',
            'IR' => 'KG',
            'IS' => 'KG',
            'IT' => 'KG',
            'JE' => 'KG',
            'JM' => 'KG',
            'JO' => 'KG',
            'JP' => 'KG',
            'KE' => 'LB',
            'KG' => 'KG',
            'KH' => 'KG',
            'KI' => 'KG',
            'KM' => 'KG',
            'KN' => 'KG',
            'KP' => 'KG',
            'KR' => 'LB',
            'KV' => 'KG',
            'KW' => 'KG',
            'KY' => 'KG',
            'KZ' => 'KG',
            'LA' => 'KG',
            'LB' => 'KG',
            'LC' => 'KG',
            'LI' => 'KG',
            'LK' => 'KG',
            'LR' => 'KG',
            'LS' => 'KG',
            'LT' => 'KG',
            'LU' => 'KG',
            'LV' => 'KG',
            'LY' => 'KG',
            'MA' => 'KG',
            'MC' => 'KG',
            'MD' => 'KG',
            'ME' => 'KG',
            'MG' => 'KG',
            'MH' => 'KG',
            'MK' => 'KG',
            'ML' => 'KG',
            'MM' => 'KG',
            'MN' => 'LB',
            'MO' => 'KG',
            'MP' => 'KG',
            'MQ' => 'KG',
            'MR' => 'KG',
            'MS' => 'KG',
            'MT' => 'KG',
            'MU' => 'KG',
            'MV' => 'KG',
            'MW' => 'KG',
            'MX' => 'KG',
            'MY' => 'KG',
            'MZ' => 'KG',
            'NA' => 'KG',
            'NC' => 'KG',
            'NE' => 'KG',
            'NG' => 'KG',
            'NI' => 'KG',
            'NL' => 'KG',
            'NO' => 'KG',
            'NP' => 'KG',
            'NR' => 'KG',
            'NU' => 'KG',
            'NZ' => 'KG',
            'OM' => 'KG',
            'PA' => 'KG',
            'PE' => 'KG',
            'PF' => 'LB',
            'PG' => 'KG',
            'PH' => 'KG',
            'PK' => 'KG',
            'PL' => 'KG',
            'PR' => 'KG',
            'PT' => 'KG',
            'PW' => 'KG',
            'PY' => 'LB',
            'QA' => 'KG',
            'RE' => 'KG',
            'RO' => 'KG',
            'RS' => 'KG',
            'RU' => 'KG',
            'RW' => 'LB',
            'SA' => 'KG',
            'SB' => 'KG',
            'SC' => 'LB',
            'SD' => 'KG',
            'SE' => 'LB',
            'SG' => 'LB',
            'SH' => 'KG',
            'SI' => 'KG',
            'SK' => 'KG',
            'SL' => 'LB',
            'SM' => 'LB',
            'SN' => 'LB',
            'SO' => 'LB',
            'SR' => 'LB',
            'SS' => 'KG',
            'ST' => 'LB',
            'SV' => 'KG',
            'SY' => 'KG',
            'SZ' => 'KG',
            'TC' => 'KG',
            'TD' => 'KG',
            'TG' => 'KG',
            'TH' => 'KG',
            'TJ' => 'KG',
            'TL' => 'KG',
            'TN' => 'KG',
            'TO' => 'KG',
            'TR' => 'KG',
            'TT' => 'KG',
            'TV' => 'KG',
            'TW' => 'KG',
            'TZ' => 'KG',
            'UA' => 'KG',
            'UG' => 'KG',
            'US' => 'KG',
            'UY' => 'KG',
            'UZ' => 'KG',
            'VC' => 'KG',
            'VE' => 'KG',
            'VG' => 'KG',
            'VI' => 'KG',
            'VN' => 'KG',
            'VU' => 'KG',
            'WS' => 'KG',
            'XB' => 'KG',
            'XC' => 'KG',
            'XE' => 'KG',
            'XM' => 'KG',
            'XN' => 'KG',
            'XS' => 'KG',
            'XY' => 'KG',
            'YE' => 'KG',
            'YT' => 'KG',
            'ZA' => 'KG',
            'ZM' => 'KG',
            'ZW' => 'KG',
        );
        $response = isset($c[$countryCode])?$c[$countryCode]:'KG';
        return $response;
    }

    public function getDimensionUnitByCountry($countryCode){
        $c = array(
            'AD' => 'CM',
            'AE' => 'CM',
            'AF' => 'IN',
            'AG' => 'CM',
            'AI' => 'CM',
            'AL' => 'CM',
            'AM' => 'CM',
            'AN' => 'CM',
            'AO' => 'IN',
            'AR' => 'CM',
            'AS' => 'CM',
            'AT' => 'IN',
            'AU' => 'CM',
            'AW' => 'CM',
            'AZ' => 'IN',
            'BA' => 'CM',
            'BB' => 'CM',
            'BD' => 'CM',
            'BE' => 'CM',
            'BF' => 'CM',
            'BG' => 'CM',
            'BH' => 'CM',
            'BI' => 'IN',
            'BJ' => 'CM',
            'BM' => 'CM',
            'BN' => 'IN',
            'BO' => 'CM',
            'BR' => 'CM',
            'BS' => 'CM',
            'BT' => 'IN',
            'BW' => 'CM',
            'BY' => 'CM',
            'BZ' => 'CM',
            'CA' => 'CM',
            'CD' => 'CM',
            'CF' => 'CM',
            'CG' => 'CM',
            'CH' => 'CM',
            'CI' => 'CM',
            'CK' => 'CM',
            'CL' => 'CM',
            'CM' => 'CM',
            'CN' => 'CM',
            'CO' => 'CM',
            'CR' => 'IN',
            'CU' => 'IN',
            'CV' => 'CM',
            'CY' => 'CM',
            'CZ' => 'CM',
            'DE' => 'CM',
            'DJ' => 'CM',
            'DK' => 'CM',
            'DM' => 'IN',
            'DO' => 'CM',
            'DZ' => 'CM',
            'EC' => 'CM',
            'EE' => 'IN',
            'EG' => 'CM',
            'ER' => 'CM',
            'ES' => 'CM',
            'ET' => 'CM',
            'FI' => 'CM',
            'FJ' => 'CM',
            'FK' => 'CM',
            'FM' => 'CM',
            'FO' => 'CM',
            'FR' => 'CM',
            'GA' => 'IN',
            'GB' => 'CM',
            'GD' => 'IN',
            'GE' => 'CM',
            'GF' => 'CM',
            'GG' => 'CM',
            'GH' => 'IN',
            'GI' => 'CM',
            'GL' => 'CM',
            'GM' => 'CM',
            'GN' => 'CM',
            'GP' => 'CM',
            'GQ' => 'CM',
            'GR' => 'CM',
            'GT' => 'CM',
            'GU' => 'CM',
            'GW' => 'CM',
            'GY' => 'CM',
            'HK' => 'CM',
            'HN' => 'CM',
            'HR' => 'IN',
            'HT' => 'CM',
            'HU' => 'CM',
            'IC' => 'IN',
            'ID' => 'CM',
            'IE' => 'CM',
            'IL' => 'IN',
            'IN' => 'CM',
            'IQ' => 'CM',
            'IR' => 'CM',
            'IS' => 'CM',
            'IT' => 'CM',
            'JE' => 'CM',
            'JM' => 'CM',
            'JO' => 'CM',
            'JP' => 'CM',
            'KE' => 'IN',
            'KG' => 'CM',
            'KH' => 'CM',
            'KI' => 'CM',
            'KM' => 'CM',
            'KN' => 'CM',
            'KP' => 'CM',
            'KR' => 'IN',
            'KV' => 'CM',
            'KW' => 'CM',
            'KY' => 'CM',
            'KZ' => 'CM',
            'LA' => 'CM',
            'LB' => 'CM',
            'LC' => 'CM',
            'LI' => 'CM',
            'LK' => 'CM',
            'LR' => 'CM',
            'LS' => 'CM',
            'LT' => 'CM',
            'LU' => 'CM',
            'LV' => 'CM',
            'LY' => 'CM',
            'MA' => 'CM',
            'MC' => 'CM',
            'MD' => 'CM',
            'ME' => 'CM',
            'MG' => 'CM',
            'MH' => 'CM',
            'MK' => 'CM',
            'ML' => 'CM',
            'MM' => 'CM',
            'MN' => 'IN',
            'MO' => 'CM',
            'MP' => 'CM',
            'MQ' => 'CM',
            'MR' => 'CM',
            'MS' => 'CM',
            'MT' => 'CM',
            'MU' => 'CM',
            'MV' => 'CM',
            'MW' => 'CM',
            'MX' => 'CM',
            'MY' => 'CM',
            'MZ' => 'CM',
            'NA' => 'CM',
            'NC' => 'CM',
            'NE' => 'CM',
            'NG' => 'CM',
            'NI' => 'CM',
            'NL' => 'CM',
            'NO' => 'CM',
            'NP' => 'CM',
            'NR' => 'CM',
            'NU' => 'CM',
            'NZ' => 'CM',
            'OM' => 'CM',
            'PA' => 'CM',
            'PE' => 'CM',
            'PF' => 'IN',
            'PG' => 'CM',
            'PH' => 'CM',
            'PK' => 'CM',
            'PL' => 'CM',
            'PR' => 'CM',
            'PT' => 'CM',
            'PW' => 'CM',
            'PY' => 'IN',
            'QA' => 'CM',
            'RE' => 'CM',
            'RO' => 'CM',
            'RS' => 'CM',
            'RU' => 'CM',
            'RW' => 'IN',
            'SA' => 'CM',
            'SB' => 'CM',
            'SC' => 'IN',
            'SD' => 'CM',
            'SE' => 'IN',
            'SG' => 'IN',
            'SH' => 'CM',
            'SI' => 'CM',
            'SK' => 'CM',
            'SL' => 'IN',
            'SM' => 'IN',
            'SN' => 'IN',
            'SO' => 'IN',
            'SR' => 'IN',
            'SS' => 'CM',
            'ST' => 'IN',
            'SV' => 'CM',
            'SY' => 'CM',
            'SZ' => 'CM',
            'TC' => 'CM',
            'TD' => 'CM',
            'TG' => 'CM',
            'TH' => 'CM',
            'TJ' => 'CM',
            'TL' => 'CM',
            'TN' => 'CM',
            'TO' => 'CM',
            'TR' => 'CM',
            'TT' => 'CM',
            'TV' => 'CM',
            'TW' => 'CM',
            'TZ' => 'CM',
            'UA' => 'CM',
            'UG' => 'CM',
            'US' => 'CM',
            'UY' => 'CM',
            'UZ' => 'CM',
            'VC' => 'CM',
            'VE' => 'CM',
            'VG' => 'CM',
            'VI' => 'CM',
            'VN' => 'CM',
            'VU' => 'CM',
            'WS' => 'CM',
            'XB' => 'CM',
            'XC' => 'CM',
            'XE' => 'CM',
            'XM' => 'CM',
            'XN' => 'CM',
            'XS' => 'CM',
            'XY' => 'CM',
            'YE' => 'CM',
            'YT' => 'CM',
            'ZA' => 'CM',
            'ZM' => 'CM',
            'ZW' => 'CM',
        );
        $response = isset($c[$countryCode])?$c[$countryCode]:'KG';
        return $response;
    }
}