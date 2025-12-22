<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/user_logic.php';
require_once __DIR__ . '/../includes/product_logic.php';
require_once __DIR__ . '/../includes/order_logic.php';

echo "<h1>Order Logic Test Suite</h1>";

// 1. SETUP: We need a User and a Product to test an Order
$user_id = 1; // Existing admin from seed
$product_id = 1; // Existing Paracetamol from seed

// Get initial stock to compare later
$initial_product = getProductById($pdo, $product_id);
$initial_stock = $initial_product['stock_quantity'];

// 2. TEST: CREATE ORDER
$items_to_buy = [
    ['product_id' => $product_id, 'quantity' => 2, 'price' => 12.00]
];

$order_res = createOrder($pdo, $user_id, $items_to_buy);

if ($order_res['success']) {
    echo "<div style='color:green;'>[PASS] Order Created. ID: " . $order_res['order_id'] . "</div>";
    
    // 3. TEST: STOCK DEDUCTION VERIFICATION
    $updated_product = getProductById($pdo, $product_id);
    if ($updated_product['stock_quantity'] == ($initial_stock - 2)) {
        echo "<div style='color:green;'>[PASS] Stock correctly reduced from $initial_stock to " . $updated_product['stock_quantity'] . "</div>";
    } else {
        echo "<div style='color:red;'>[FAIL] Stock not updated correctly!</div>";
    }

    // 4. TEST: FETCH ORDER DETAILS
    $details = getOrderDetails($pdo, $order_res['order_id']);
    if ($details && count($details['items']) > 0) {
        echo "<div style='color:green;'>[PASS] Order details retrieved with " . count($details['items']) . " item(s).</div>";
    } else {
        echo "<div style='color:red;'>[FAIL] Could not retrieve order items.</div>";
    }
} else {
    echo "<div style='color:red;'>[FAIL] Order creation failed: " . $order_res['message'] . "</div>";
}