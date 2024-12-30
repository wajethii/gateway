<?php
require 'config.php';

function generateToken()
{
    $url = BASE_URL . "/oauth/v1/generate?grant_type=client_credentials";

    $credentials = base64_encode(CONSUMER_KEY . ":" . CONSUMER_SECRET);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . $credentials
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response);
    return $result->access_token ?? null;
}
?>
