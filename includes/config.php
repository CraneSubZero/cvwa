// FILE: includes/config.php
// FIX: Added missing session_start() and improved base URL function

<?php
session_start(); // ADDED: Required for security level functionality

// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cvwa';

// Establish database connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Security Level Configuration
if (!isset($_SESSION['security_level'])) {
    $_SESSION['security_level'] = 0; // Default to low
}

// Application Configuration
define('APP_NAME', 'CVWA');
define('APP_VERSION', '1.0.0');

// IMPROVED: Base URL function
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $folder = dirname($script);
    
    // Handle different directory structures
    if ($folder === '/') {
        return $protocol . $host . '/';
    } else {
        return $protocol . $host . $folder . '/';
    }
}
?>