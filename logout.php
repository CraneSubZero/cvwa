<?php
session_start();

// Log the logout activity if user was logged in
if (isset($_SESSION['user_id'])) {
    include('includes/config.php');
    include('includes/functions.php');
    
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>