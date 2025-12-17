<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/user_logic.php';

echo "<h1>User Logic Test Suite</h1>";
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
// TEST 1: REGISTRATION
// ==========================================
$unique_id = time(); // Unique ID to prevent "Email already exists" error on refresh
$test_email = "test_user_{$unique_id}@example.com";
$test_pass = "Pass123!";
$test_name = "Test User {$unique_id}";

$reg_result = registerUser($pdo, $test_name, $test_email, $test_pass, 'member', '0123456789', '123 Test Lane');

testLog("Register User", $reg_result['success'], "- " . $reg_result['message']);

// ==========================================
// TEST 2: LOGIN (AUTHENTICATION)
// ==========================================
$user = authenticateUser($pdo, $test_email, $test_pass);
testLog("Login Correct Password", $user !== false, "- Logged in as ID: " . ($user['id'] ?? 'N/A'));

$fail_user = authenticateUser($pdo, $test_email, "WrongPass");
testLog("Login Wrong Password", $fail_user === false, "- Correctly rejected invalid password");

// ==========================================
// TEST 3: UPDATE PROFILE
// ==========================================
if ($user) {
    $new_name = "Updated Name {$unique_id}";
    $update_result = updateUser($pdo, $user['id'], $new_name, '999999999', 'Updated Address');

    // Fetch again to verify
    $updated_user = getUserById($pdo, $user['id']);
    testLog("Update Profile", $updated_user['full_name'] === $new_name, "- Name changed to: {$updated_user['full_name']}");
}

// ==========================================
// TEST 4: CHANGE PASSWORD
// ==========================================
if ($user) {
    $new_password = "NewPassword456!";

    // 1. Change the password in DB
    $pw_change_result = changePassword($pdo, $user['id'], $new_password);
    testLog("Change Password Action", $pw_change_result, "- Database updated");

    // 2. Verify OLD password fails
    $auth_old = authenticateUser($pdo, $test_email, $test_pass);
    testLog("Auth with OLD Password", $auth_old === false, "- Old password rejected");

    // 3. Verify NEW password works
    $auth_new = authenticateUser($pdo, $test_email, $new_password);
    testLog("Auth with NEW Password", $auth_new !== false, "- New password accepted");
}

// ==========================================
// TEST 5: GET ALL USERS (ADMIN FUNCTION)
// ==========================================
$all_users = getAllUsers($pdo);
$has_users = count($all_users) > 0;
// Verify our current test user is in the list
$found_current = false;
foreach ($all_users as $u) {
    if ($u['email'] === $test_email) {
        $found_current = true;
        break;
    }
}

testLog("Get All Users", $has_users && $found_current, "- Count: " . count($all_users) . " (Found current test user in list)");

// Test Role Filter (Optional Check)
$member_users = getAllUsers($pdo, 'member');
testLog("Get All Users (Role Filter)", is_array($member_users), "- Fetched member list successfully");


// ==========================================
// TEST 6: DELETE USER (CLEANUP)
// ==========================================
if ($user) {
    $del_result = deleteUser($pdo, $user['id']);
    $check_user = getUserById($pdo, $user['id']);

    testLog("Delete User", $del_result && $check_user === false, "- User removed from DB");
}

echo "<hr><p><em>Tests Completed. Refresh page to run again with new random data.</em></p>";
