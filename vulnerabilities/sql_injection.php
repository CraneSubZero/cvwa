<?php
include('../config/config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $id"; // No sanitization
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Username: " . $row['username'] . "<br>";
    }
}
?>