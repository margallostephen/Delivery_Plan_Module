<?php
function fetchDataFromDataPhp(string $url, bool $returnDecoded = true)
{
    $baseUrl = "http://localhost/Delivery_Plan_Module/";
    $url = $baseUrl . $url;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($returnDecoded) {
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        return $decoded;
    } else {
        return $response;
    }
}
