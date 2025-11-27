<?php
// Simple database update script
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rhymes_platform";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update the enum values
    $sql = "ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')";
    $pdo->exec($sql);
    
    echo "Successfully updated the area enum values in rev_sync_logs table.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$pdo = null;