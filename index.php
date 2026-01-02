<?php
require __DIR__ . '/vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// ================= MIDTRANS CONFIG =================
// GANTI DENGAN KEY SANDBOX PUNYA KAMU
Config::$serverKey = 'Mid-server-n0lw4lVD4DWswj-r6Kv7ExCL';
Config::$clientKey = 'Mid-client-s3Fpp8DYlCOiqsAL';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

// ================= ONLY POST =================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(200);
    echo "OK";
    exit;
}

// ================= TRANSACTION DATA =================
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

// ================= GENERATE SNAP TOKEN =================
try {
    $snapToken = Snap::getSnapToken($params);
    header('Content-Type: application/json');
    echo json_encode([
        'token' => $snapToken
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
