<?php
// create_transaction.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Server Key SANDBOX (ganti dengan milik Anda)
$server_key = "Mid-server-n0lw4lVD4DWswj-r6Kv7ExCL";

// Terima data dari Android
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validasi input
if (!isset($data['order_id']) || !isset($data['gross_amount']) || !isset($data['title'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

$order_id = $data['order_id'];
$gross_amount = (int) $data['gross_amount'];
$title = $data['title'];
$customer_name = $data['customer_name'] ?? 'Customer';
$customer_email = $data['customer_email'] ?? 'customer@example.com';

// Prepare Midtrans Payload
$payload = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $gross_amount
    ],
    'item_details' => [
        [
            'id' => $order_id,
            'price' => $gross_amount,
            'quantity' => 1,
            'name' => $title
        ]
    ],
    'customer_details' => [
        'first_name' => $customer_name,
        'email' => $customer_email
    ]
];

// Hit Midtrans API (SANDBOX)
$url = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verify untuk localhost
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode($server_key . ':')
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle CURL error
if ($curl_error) {
    echo json_encode([
        'success' => false,
        'message' => 'CURL Error',
        'error' => $curl_error
    ]);
    exit;
}

// Return Response
if ($http_code == 201) {
    $result = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'snap_token' => $result['token'],
        'redirect_url' => $result['redirect_url']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal generate token',
        'http_code' => $http_code,
        'error' => json_decode($response, true)
    ]);
}
?>
