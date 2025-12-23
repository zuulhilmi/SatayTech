<?php

/**
 * CREATE A NEW ORDER (Checkout)
 * * @param PDO $pdo The database connection object.
 * @param int $user_id The ID of the user placing the order.
 * @param array $items Array of items: [['product_id' => 1, 'quantity' => 2, 'price' => 12.00], ...]
 * @param string $payment_method Default is 'Credit Card'.
 * @return array Returns ['success' => bool, 'message' => string, 'order_id' => int|null].
 */
function createOrder($pdo, $user_id, $items, $payment_method = 'Credit Card')
{
    try {
        // 1. Start a transaction to ensure data integrity
        $pdo->beginTransaction();

        // 2. Calculate total amount
        $total_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // 3. Insert into 'orders' table
        $sql_order = "INSERT INTO orders (user_id, total_amount, payment_method, payment_status) VALUES (?, ?, ?, 'paid')";
        $stmt_order = $pdo->prepare($sql_order);
        $stmt_order->execute([$user_id, $total_amount, $payment_method]);
        
        // Get the ID of the order we just created
        $order_id = $pdo->lastInsertId();

        // 4. Insert each item into 'order_items' table
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);

        foreach ($items as $item) {
            $stmt_item->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

            // 5. Update product stock (Subtract quantity)
            $sql_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $stmt_stock = $pdo->prepare($sql_stock);
            $stmt_stock->execute([$item['quantity'], $item['product_id']]);
        }

        // Commit all changes to the database
        $pdo->commit();

        return ["success" => true, "message" => "Order placed successfully!", "order_id" => $order_id];

    } catch (PDOException $e) {
        // If anything goes wrong, roll back every change made during this process
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ["success" => false, "message" => "Order failed: " . $e->getMessage(), "order_id" => null];
    }
}

/**
 * GET ORDER HISTORY FOR A USER
 */
function getUserOrders($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * GET FULL ORDER DETAILS (Order + Items)
 */
function getOrderDetails($pdo, $order_id)
{
    // Get Order Header
    $stmt_order = $pdo->prepare("SELECT o.*, u.full_name, u.email 
                                 FROM orders o 
                                 JOIN users u ON o.user_id = u.id 
                                 WHERE o.id = ?");
    $stmt_order->execute([$order_id]);
    $order = $stmt_order->fetch();

    if (!$order) return false;

    // Get Items in that Order
    $stmt_items = $pdo->prepare("SELECT oi.*, p.name as product_name 
                                  FROM order_items oi 
                                  LEFT JOIN products p ON oi.product_id = p.id 
                                  WHERE oi.order_id = ?");
    $stmt_items->execute([$order_id]);
    $order['items'] = $stmt_items->fetchAll();

    return $order;
}

/**
 * GET ALL ORDERS (Admin Report)
 */
function getAllOrders($pdo)
{
    $stmt = $pdo->query("SELECT o.*, u.full_name FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         ORDER BY o.order_date DESC");
    return $stmt->fetchAll();
}