<?php
// FILE: vulnerabilities/csrf/simulation.php
// PURPOSE: Demonstrates CSRF vulnerabilities

declare(strict_types=1);

require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../../login.php', 'Please login to access this page');
}

$pageTitle = "CSRF Simulation - CVWA";
include '../../includes/header.php';

$message = '';

// Process password change simulation
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['change_pass'])) {
    // Simulate CSRF vulnerability
    $_SESSION['password'] = $_GET['change_pass'];
    $message = "Password changed to: " . htmlspecialchars($_GET['change_pass']);
    logActivity($_SESSION['user_id'], 'csrf_exploit', "Password changed via CSRF");
}

// Vulnerability description
$description = <<<HTML
<div class="alert alert-info">
    <strong>Current Security Level:</strong> {$_SESSION['security_level']} - {getSecurityLevelName()}
</div>

<p>CSRF (Cross-Site Request Forgery) vulnerabilities occur when an application accepts state-changing 
requests without verifying the request's origin or requiring user confirmation.</p>

<h5 class="mt-3">Testing Techniques:</h5>
<ul>
    <li>Create a malicious page that submits forms to this application</li>
    <li>Use image tags to trigger GET requests</li>
    <li>Try AJAX requests from other domains</li>
</ul>
HTML;

// Build the content
$content = <<<HTML
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Simulated Password Change</h5>
        
        {$message ? "<div class='alert alert-success'>$message</div>" : ""}
        
        <div class="mb-3">
            <a href="?change_pass=123456" class="btn btn-danger">
                Simulate Password Change (GET)
            </a>
        </div>
        
        <form method="GET" class="mb-3">
            <input type="hidden" name="change_pass" value="hacked123">
            <button type="submit" class="btn btn-danger">
                Simulate Password Change (Hidden Form)
            </button>
        </form>
        
        <div class="alert alert-warning">
            <strong>Warning:</strong> These links demonstrate CSRF vulnerabilities. 
            In a real application, sensitive actions should require CSRF tokens.
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>CSRF Protection Examples</h5>
    </div>
    <div class="card-body">
        <h6>Using CSRF Tokens:</h6>
        <pre><code>&lt;form method="POST"&gt;
    &lt;input type="hidden" name="csrf_token" value="&lt;?= $_SESSION['csrf_token'] ?&gt;"&gt;
    &lt;!-- form fields --&gt;
&lt;/form&gt;

// Server-side validation
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}</code></pre>
        
        <h6 class="mt-3">SameSite Cookies:</h6>
        <pre><code>// In PHP configuration or session_start() parameters
session_set_cookie_params([
    'samesite' => 'Strict',
    'secure' => true,
    'httponly' => true
]);</code></pre>
    </div>
</div>
HTML;

displayVulnerabilityTemplate('CSRF Simulation', $description, getSecurityLevelName(), $content);
include '../../includes/footer.php';