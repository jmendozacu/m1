<?php
$curl = curl_init('https://secure.authorize.net/gateway/transact.dll');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$response = curl_exec($curl);
echo curl_error($curl);
echo $response;


