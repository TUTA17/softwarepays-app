<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `keygame_keyngon` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database created successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
