// FILE: login.php
// FIX: Added session_start() at top and improved security level check

<?php
session_start(); // ADDED: Required for authentication
include('includes/config.php');
include('includes/functions.php');

// Check if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// FIX: Initialize security level if not set
if (!isset($_SESSION['security_level'])) {
    $_SESSION['security_level'] = 0;
}

[... rest of your existing login.php code ...]
?>