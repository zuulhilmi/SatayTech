<?php

/**
 * REGISTER NEW USER
 * Handles password hashing automatically.
 *
 * @param PDO $pdo The database connection object.
 * @param string $full_name The user's full name.
 * @param string $email The user's email address.
 * @param string $password The raw password (will be hashed after).
 * @param string $role The user role ('member' or 'admin'). Default is 'member'.
 * @param string|null $phone The user's phone number (optional).
 * @param string|null $address The user's address (optional).
 * @return array Returns ['success' => bool, 'message' => string].
 */
function registerUser($pdo, $full_name, $email, $password, $role = 'member', $phone = null, $address = null)
{
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        return ["success" => false, "message" => "Email already registered."];
    }

    // Hash the raw password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare insert statement
    $sql = "INSERT INTO users (full_name, email, password, role, phone_number, address) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$full_name, $email, $hashed_password, $role, $phone, $address]);
        return ["success" => true, "message" => "Registration successful!"];
    } catch (PDOException $e) {
        return ["success" => false, "message" => "Database error: " . $e->getMessage()];
    }
}

/**
 * AUTHENTICATE USER / LOGIN
 *
 * @param PDO $pdo The database connection object.
 * @param string $email The email to check.
 * @param string $password The raw password to verify.
 * @return array|false Returns the user row array on success, or false on failure.
 */
function authenticateUser($pdo, $email, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false; // Invalid credentials
}

/**
 * GET USER BY ID
 * 
 * @param PDO $pdo The database connection object.
 * @param int $id The user ID.
 * @return array|false Returns the user row array, or false if not found.
 */
function getUserById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT id, full_name, email, role, phone_number, address, created_at FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * UPDATE USER PROFILE
 * Handles updates for normal info (no password)
 * 
 * @param PDO $pdo The database connection object.
 * @param int $id The user ID to update.
 * @param string $full_name The new full name.
 * @param string $phone The new phone number.
 * @param string $address The new address.
 * @return bool Returns true on success, false on failure.
 */
function updateUser($pdo, $id, $full_name, $phone, $address)
{
    $sql = "UPDATE users SET full_name = ?, phone_number = ?, address = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$full_name, $phone, $address, $id]);
}

/**
 * UPDATE PASSWORD
 * Specific function just for password changing
 * 
 * @param PDO $pdo The database connection object.
 * @param int $id The user ID.
 * @param string $new_password The new raw password (will be hashed).
 * @return bool Returns true on success, false on failure.
 */
function changePassword($pdo, $id, $new_password)
{
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed_password, $id]);
}

/**
 * GET ALL USERS (Admin Only Function)
 * Optional filter by role
 * 
 * @param PDO $pdo The database connection object.
 * @param string|null $role Optional role filter ('member' or 'admin').
 * @return array Returns an array of user arrays.
 */
function getAllUsers($pdo, $role = null)
{
    if ($role) {
        $stmt = $pdo->prepare("SELECT id, full_name, email, role, created_at FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
    } else {
        $stmt = $pdo->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
    }
    return $stmt->fetchAll();
}

/**
 * DELETE USER (Admin Only Function)
 * 
 * @param PDO $pdo The database connection object.
 * @param int $id The user ID to delete.
 * @return bool Returns true on success, false on failure.
 */
function deleteUser($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
