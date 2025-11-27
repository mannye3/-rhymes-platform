<?php

$host = '127.0.0.1';
$dbname = 'rhymes_platform';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Connected successfully to MySQL database '$dbname'\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total books in database: " . $result['count'] . "\n";
    
    // Check for accepted books without rev_book_id
    $stmt = $pdo->query("SELECT id, title, status, rev_book_id FROM books WHERE status = 'accepted' AND rev_book_id IS NULL ORDER BY updated_at DESC LIMIT 5");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Accepted books without ERPREV ID:\n";
    foreach ($books as $book) {
        echo "  ID: {$book['id']}, Title: {$book['title']}\n";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}