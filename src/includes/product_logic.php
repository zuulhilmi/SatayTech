<?php

/**
 * CREATE NEW PRODUCT (Admin Only)
 * * @param PDO $pdo The database connection object.
 * @param int $category_id The ID of the category.
 * @param string $name Product name.
 * @param string $description Product description.
 * @param float $price Product price.
 * @param int $stock Product stock quantity.
 * @param string|null $image_path Path to the product image.
 * @return array Returns ['success' => bool, 'message' => string].
 */
function createProduct($pdo, $category_id, $name, $description, $price, $stock, $image_path = null)
{
    $sql = "INSERT INTO products (category_id, name, description, price, stock_quantity, image_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$category_id, $name, $description, $price, $stock, $image_path]);
        return ["success" => true, "message" => "Product added successfully!"];
    } catch (PDOException $e) {
        return ["success" => false, "message" => "Database error: " . $e->getMessage()];
    }
}

/**
 * GET ALL PRODUCTS
 * Includes category name via JOIN.
 * * @param PDO $pdo The database connection object.
 * @param int|null $category_id Optional filter by category.
 * @return array Returns an array of product arrays.
 */
function getAllProducts($pdo, $category_id = null)
{
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id";
    
    if ($category_id) {
        $sql .= " WHERE p.category_id = ? ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id]);
    } else {
        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $pdo->query($sql);
    }
    
    return $stmt->fetchAll();
}

/**
 * GET PRODUCT BY ID
 * * @param PDO $pdo The database connection object.
 * @param int $id The product ID.
 * @return array|false Returns the product row or false if not found.
 */
function getProductById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * UPDATE PRODUCT
 * * @param PDO $pdo The database connection object.
 * @param int $id Product ID to update.
 * @param array $data Associative array of fields to update (name, price, etc).
 * @return bool
 */
function updateProduct($pdo, $id, $category_id, $name, $description, $price, $stock, $image_path = null)
{
    $sql = "UPDATE products 
            SET category_id = ?, name = ?, description = ?, price = ?, stock_quantity = ?, image_path = ? 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$category_id, $name, $description, $price, $stock, $image_path, $id]);
}

/**
 * DELETE PRODUCT
 * * @param PDO $pdo The database connection object.
 * @param int $id The product ID.
 * @return bool
 */
function deleteProduct($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * GET ALL CATEGORIES
 * Useful for populating dropdown menus in forms.
 * * @param PDO $pdo The database connection object.
 * @return array
 */
function getAllCategories($pdo)
{
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

/**
 * UPDATE STOCK QUANTITY
 * Specifically for adjusting inventory after a purchase.
 * * @param PDO $pdo The database connection object.
 * @param int $id Product ID.
 * @param int $quantity The amount to subtract (use negative for addition).
 * @return bool
 */
function updateProductStock($pdo, $id, $quantity)
{
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    return $stmt->execute([$quantity, $id]);
}