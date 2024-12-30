<?php
require 'config.php';
require 'generate_token.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = generateToken();

    if (!$token) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to generate token']);
        exit;
    }

    $amount = $_POST['amount'];
    $phone = $_POST['phone'];
    $accountReference = "mmNetPayment";
    $transactionDesc = "mmNet Subscription";

    $timestamp = date("YmdHis");
    $password = base64_encode(BUSINESS_SHORTCODE . PASSKEY . $timestamp);

    $payload = [
        "BusinessShortCode" => BUSINESS_SHORTCODE,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => BUSINESS_SHORTCODE,
        "PhoneNumber" => $phone,
        "CallBackURL" => "https://yourdomain.com/callback",
        "AccountReference" => $accountReference,
        "TransactionDesc" => $transactionDesc
    ];

    $url = BASE_URL . "/mpesa/stkpush/v1/processrequest";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
}
?>
