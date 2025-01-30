<?php
require_once 'vendor/autoload.php'; // Include Paystack SDK if using Composer

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get Paystack Secret Key from environment
$secretKey = $_ENV['PAYSTACK_SECRET_KEY'];

// Capture the payment reference from the request
$input = json_decode(file_get_contents('php://input'), true);
$reference = $input['reference'] ?? null;

if (!$reference) {
    echo json_encode(['status' => 'failed', 'message' => 'Missing payment reference']);
    exit;
}

// Verify the payment with Paystack API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/{$reference}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$secretKey}"
]);

$response = curl_exec($ch);
curl_close($ch);

// Decode the response from Paystack
$responseData = json_decode($response, true);

// Handle the payment verification response
if ($responseData['status'] == 'success' && $responseData['data']['status'] == 'success') {
    // Payment was successful
    echo json_encode(['status' => 'success']);
} else {
    // Payment failed or was unsuccessful
    echo json_encode(['status' => 'failed', 'message' => 'Payment failed']);
}
?>
