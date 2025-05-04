<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['change_pass'])) {
        // Simulate CSRF vulnerability
        $_SESSION['password'] = $_GET['change_pass'];
        echo "Password changed to: " . $_SESSION['password'];
    }
}
?>
<a href="csrf_simulation.php?change_pass=hack123">Change Password</a>