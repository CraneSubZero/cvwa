<?php
// FILE: vulnerabilities/sql_injection/mitigation.php
// PURPOSE: SQL injection prevention guide

require_once '../../includes/header.php';
?>

<div class="container">
    <h2>SQL Injection Prevention</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4>Defense Techniques</h4>
        </div>
        <div class="card-body">
            <h5>1. Prepared Statements (Parameterized Queries)</h5>
            <pre>
// Using PDO
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

// Using MySQLi
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();</pre>

            <h5 class="mt-4">2. Input Validation</h5>
            <pre>
// Whitelist validation
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    die("Invalid username");
}</pre>

            <h5 class="mt-4">3. Least Privilege Principle</h5>
            <p>Database users should have minimal required permissions</p>

            <h5 class="mt-4">4. Web Application Firewall (WAF)</h5>
            <p>Can help filter out malicious SQL patterns</p>

            <h5 class="mt-4">5. Error Handling</h5>
            <pre>
// Don't expose database errors to users
set_exception_handler(function($e) {
    error_log($e->getMessage());
    die("A database error occurred");
});</pre>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>