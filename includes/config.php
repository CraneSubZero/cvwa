<?php
// FILE: includes/config.php
// PURPOSE: Central configuration and database connection

declare(strict_types=1);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Session configuration - only set if session isn't active
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}

// Database Configuration
const DB_HOST = 'localhost';
const DB_USER = 'cvwa_user';
const DB_PASS = 'secure_password';
const DB_NAME = 'cvwa_db';

// Establish database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Security Level Configuration
if (!isset($_SESSION['security_level'])) {
    $_SESSION['security_level'] = 0; // Default to low
}

// Application Constants
define('APP_NAME', 'CVWA');
define('APP_VERSION', '2.0.0');
define('BASE_URL', getBaseUrl());

// Get base URL function
function getBaseUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS'])) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    
    // Remove script filename if not index.php
    $path = str_replace('/index.php', '', dirname($script));
    
    return rtrim($protocol . $host . $path, '/') . '/';
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>