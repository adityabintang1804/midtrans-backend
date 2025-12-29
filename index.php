<?php
header("Content-Type: application/json");

$serverKey = getenv("MIDTRANS_SERVER_KEY");

if (!$serverKey) {
    http_response_code(500);
    echo json_encode(["error" => "Server key not set"]);
    exit;
}

$payload = [
    "transaction_details" => [
        "order_id" => "ORDER-" . time(),
        "gross_amount" => 10000
    ],
    "customer_details" => [
        "first_name" => "Mahasiswa",
        "email" => "mahasiswa@mail.com"
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

if ($response === false) {
    echo json_encode(["error" => curl_error($ch)]);
}

curl_close($ch);

echo $response;
