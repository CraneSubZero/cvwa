<?php
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    echo "Search results for: " . $search; // Reflected XSS vulnerability
}
?>