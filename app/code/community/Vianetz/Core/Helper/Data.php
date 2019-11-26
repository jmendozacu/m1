<?php
/**
 * Core Data Helper class
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_Core
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 */
class Vianetz_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param      $host
     * @param      $path
     * @param      $referer
     * @param      $dataToSend
     * @param bool $proxyHost
     * @param bool $proxyPort
     *
     * @return string
     */
    public function postToHost($host, $path, $referer, $dataToSend, $proxyHost = false, $proxyPort = false)
    {
        $result = '';

        $request = '';
        foreach ($dataToSend as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        if (empty($proxyHost) || empty($proxyPort) || $proxyHost === false || $proxyPort === false) {
            $fp = fsockopen($host, 80);
            fputs($fp, "POST $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
        } else {
            $fp = fsockopen($proxyHost, $proxyPort);
            fputs($fp, "POST http://$host$path HTTP/1.1\r\n");
            fputs($fp, "Host: $proxyHost\r\n");
        }

        fputs($fp, "Referer: $referer\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($request) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $request);

        while (!feof($fp)) {
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        // Remove POST headers
        $response = substr($result, strpos($result, "\r\n\r\n") + 4);

        return $response;
    }
}
