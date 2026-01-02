<?php
require __DIR__ . '/vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

Config::$serverKey = 'SB-Mid-server-PASTE-DARI-DASHBOARD';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    echo "POST only";
    exit;
}

$params = [
    'transaction_details' => [
        'order_id' => 'ORDER-' . time(),
        'gross_amount' => 10000
    ],
    'customer_details' => [
        'first_name' => 'Adit',
        'email' => 'adit@test.com',
        'phone' => '08123456789'
    ]
];

$snapToken = Snap::getSnapToken($params);

header('Content-Type: application/json');
echo json_encode([
    'token' => $snapToken
]);
