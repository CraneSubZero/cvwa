<?php
// FILE: includes/database.php
// PURPOSE: Database connection for SQLi demos

$host = 'localhost';
$user = 'cvwa_user';
$pass = 'secure_password';
$db   = 'cvwa_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // Create vulnerable table if not exists
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(50) NOT NULL,
        email VARCHAR(100),
        is_admin TINYINT DEFAULT 0
    )");
    
    // Insert sample data
    $conn->exec("INSERT IGNORE INTO users (username, password, email, is_admin) VALUES
        ('admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin@example.com', 1),
        ('user1', '5f4dcc3b5aa765d61d8327deb882cf99', 'user1@example.com', 0),
        ('user2', '5f4dcc3b5aa765d61d8327deb882cf99', 'user2@example.com', 0)");
        
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>