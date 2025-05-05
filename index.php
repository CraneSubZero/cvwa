<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Home - CVWA";
include('includes/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Welcome to CVWA (College Vulnerable Web Application)</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This is an educational web application designed to demonstrate various web security vulnerabilities.</p>
                    <p>CVWA is intended for educational purposes only, to help students and security professionals learn about common web application vulnerabilities in a safe, controlled environment.</p>
                    
                    <div class="alert alert-warning mt-3">
                        <strong>Disclaimer:</strong> Do not deploy this application in a production environment or on a publicly accessible server. It contains intentional security vulnerabilities that could be exploited.
                    </div>
                    
                    <h5 class="mt-4">Available Vulnerability Modules:</h5>
                    <div class="list-group mt-3">
                        <a href="vulnerabilities/sql_injection/" class="list-group-item list-group-item-action">SQL Injection</a>
                        <a href="vulnerabilities/xss/" class="list-group-item list-group-item-action">Cross-Site Scripting (XSS)</a>
                        <a href="vulnerabilities/csrf/" class="list-group-item list-group-item-action">Cross-Site Request Forgery (CSRF)</a>
                        <a href="vulnerabilities/file_upload/" class="list-group-item list-group-item-action">Insecure File Upload</a>
                        <a href="vulnerabilities/command_injection/" class="list-group-item list-group-item-action">Command Injection</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>