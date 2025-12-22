<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/product_logic.php';

echo "<h1>Product Logic Test Suite</h1>";
echo "<p>Testing database connection: <strong>" . ($pdo ? "OK" : "FAIL") . "</strong></p><hr>";

// Helper to print results cleanly
function testLog($name, $condition, $message = "")
{
    if ($condition) {
        echo "<div style='color:green; margin-bottom:5px;'><strong>[PASS] $name</strong> $message</div>";
    } else {
        echo "<div style='color:red; margin-bottom:5px;'><strong>[FAIL] $name</strong> $message</div>";
    }
}

// ==========================================
// PRE-TEST: GET EXISTING CATEGORY
// ==========================================
// We need a category ID to create a product. 
// Using ID 1 (Medicine) based on your schema seed data.
$categories = getAllCategories($pdo);
$target_category_id = $categories[0]['id'] ?? 1; 
$target_category_name = $categories[0]['name'] ?? 'Default';

testLog("Fetch Categories", count($categories) > 0, "- Found " . count($categories) . " categories. Using: $target_category_name");

// ==========================================
// TEST 1: CREATE PRODUCT
// ==========================================
$unique_suffix = time();
$test_name = "Test Product {$unique_suffix}";
$test_desc = "This is a temporary test product description.";
$test_price = 19.99;
$test_stock = 50;

$create_result = createProduct($pdo, $target_category_id, $test_name, $test_desc, $test_price, $test_stock, 'test_image.jpg');

testLog("Create Product", $create_result['success'], "- " . $create_result['message']);

// ==========================================
// TEST 2: READ PRODUCT (FETCH BY ID)
// ==========================================
// Find the ID of the product we just created
$all_products = getAllProducts($pdo);
$product_id = null;
foreach ($all_products as $p) {
    if ($p['name'] === $test_name) {
        $product_id = $p['id'];
        break;
    }
}

if ($product_id) {
    $product = getProductById($pdo, $product_id);
    testLog("Get Product By ID", $product !== false, "- Found product: " . ($product['name'] ?? 'N/A'));
    testLog("Category Join Verification", isset($product['category_name']), "- Category name retrieved: " . ($product['category_name'] ?? 'N/A'));
} else {
    testLog("Get Product By ID", false, "- Could not find the created product in the list.");
}

// ==========================================
// TEST 3: UPDATE PRODUCT
// ==========================================
if ($product_id) {
    $updated_name = "Updated Product Name {$unique_suffix}";
    $update_res = updateProduct($pdo, $product_id, $target_category_id, $updated_name, $test_desc, 25.00, 60, 'updated_image.jpg');
    
    $check_update = getProductById($pdo, $product_id);
    testLog("Update Product", $update_res && $check_update['name'] === $updated_name, "- Price updated to: " . $check_update['price']);
}

// ==========================================
// TEST 4: STOCK MANAGEMENT
// ==========================================
if ($product_id) {
    // Simulate a sale of 5 items
    $sale_quantity = 5;
    $initial_stock = (int)getProductById($pdo, $product_id)['stock_quantity'];
    
    $stock_res = updateProductStock($pdo, $product_id, $sale_quantity);
    $final_stock = (int)getProductById($pdo, $product_id)['stock_quantity'];
    
    testLog("Update Stock (Deduction)", $stock_res && $final_stock === ($initial_stock - $sale_quantity), "- Stock went from $initial_stock to $final_stock");
}

// ==========================================
// TEST 5: FILTER BY CATEGORY
// ==========================================
$filtered_list = getAllProducts($pdo, $target_category_id);
$all_match = true;
foreach ($filtered_list as $p) {
    if ($p['category_id'] != $target_category_id) {
        $all_match = false;
        break;
    }
}
testLog("Filter Products by Category", count($filtered_list) > 0 && $all_match, "- All " . count($filtered_list) . " items match category ID: $target_category_id");

// ==========================================
// TEST 6: DELETE PRODUCT (CLEANUP)
// ==========================================
if ($product_id) {
    $del_result = deleteProduct($pdo, $product_id);
    $check_del = getProductById($pdo, $product_id);

    testLog("Delete Product", $del_result && $check_del === false, "- Product removed from inventory");
}

echo "<hr><p><em>Product Tests Completed. Refresh to run again.</em></p>";