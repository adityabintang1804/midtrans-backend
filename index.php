<?php
require __DIR__ . '/vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

// ================= MIDTRANS CONFIG =================
Config::$serverKey = 'SB-Mid-server-n0lw4lVD4DWswj-r6Kv7ExCL';
Config::$clientKey = 'SB-Mid-client-s3Fpp8DYlCOiqsAL';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;
Config::$appendNotifUrl = true;

// ================= HANDLE NOTIFICATION =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/notification') !== false) {
    $postData = json_decode(file_get_contents("php://input"), true);
    
    if ($postData === null) {
        $postData = $_POST;
    }
    
    $notification = Transaction::notification();
    file_put_contents(__DIR__ . '/notification_log.txt', date('Y-m-d H:i:s') . ' - ' . json_encode($notification) . "\n", FILE_APPEND);
    
    http_response_code(200);
    exit;
}

// ================= GENERATE SNAP TOKEN =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/notification') === false) {
    $postData = json_decode(file_get_contents("php://input"), true);
    
    // Ambil data dari request atau gunakan default
    $orderData = $postData ?? [];
    
    $params = [
        'transaction_details' => [
            'order_id' => 'ORDER-' . time() . '-' . rand(1000, 9999),
            'gross_amount' => intval($orderData['gross_amount'] ?? 10000)
        ],
        'customer_details' => [
            'first_name' => $orderData['first_name'] ?? 'Adit',
            'email' => $orderData['email'] ?? 'adit@test.com',
            'phone' => $orderData['phone'] ?? '08123456789',
            'billing_address' => [
                'first_name' => $orderData['first_name'] ?? 'Adit',
                'address' => $orderData['address'] ?? 'Jl. Test',
                'city' => $orderData['city'] ?? 'Jakarta',
                'postal_code' => $orderData['postal_code'] ?? '12345',
                'country_code' => 'IDN'
            ]
        ],
        'item_details' => [
            [
                'id' => 'ITEM1',
                'price' => intval($orderData['gross_amount'] ?? 10000),
                'quantity' => 1,
                'name' => 'Test Item'
            ]
        ]
    ];
    
    try {
        error_log("Snap Token Request: " . json_encode($params));
        
        $snapToken = Snap::getSnapToken($params);
        
        error_log("Snap Token Generated: " . $snapToken);
        
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'token' => $snapToken
        ]);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// ================= DEFAULT GET =================
http_response_code(200);
echo json_encode([
    'message' => 'Midtrans Backend is Running',
    'status' => 'OK',
    'server_key_set' => !empty(Config::$serverKey),
    'client_key_set' => !empty(Config::$clientKey)
]);
?>
