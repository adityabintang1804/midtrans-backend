<?php
header("Content-Type: application/json");

$serverKey = getenv("MIDTRANS_SERVER_KEY");

$payload = [
    "transaction_details" => [
        "order_id" => "ORDER-" . time(),
        "gross_amount" => 10000
    ]
];

$ch = curl_init("https://app.sandbox.midtrans.com/snap/v1/transactions");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Basic " . base64_encode($serverKey . ":")
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
