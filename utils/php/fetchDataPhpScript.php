<?php
function fetchDataFromDataPhp(string $url, bool $returnDecoded = true)
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    $url = $protocol . '://' . $host . $basePath . $url;

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
