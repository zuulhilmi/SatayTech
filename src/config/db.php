<?php
// Database Configuration
$host = 'localhost';
$dbname = 'satay-tech-db';

// Credentials
// Default for XAMPP is 'root' with an empty password
$username = 'root';
$password = '';

// NOTE: If you are on Linux/Mac and followed the README to create an admin user,
// uncomment the lines below and comment out the XAMPP defaults above.
// $username = 'admin';
// $password = 'password123';

// NOTE: This config gives a $pdo that can be used for sql query later
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Might want to disable this during production.. maybe maybe
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
