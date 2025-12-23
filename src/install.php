<?php
// Settings
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'satay-tech-db';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");

    $pdo->query("USE `$dbname`");

    $sql_file = __DIR__ . '/sql/schema.sql';

    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        $pdo->exec($sql);
        echo "<h1>Success!</h1>";
        echo "Database <code>$dbname</code> created.<br>";
    } else {
        die("Error: File /sql/schema.sql not found.");
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
