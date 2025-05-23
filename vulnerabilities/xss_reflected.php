<?php
// FILE: vulnerabilities/xss/reflected.php
// PURPOSE: Demonstrates reflected XSS vulnerability with three security levels

session_start();
include('../../includes/config.php');
include('../../includes/functions.php');

// SECURITY: Check authentication
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "XSS - CVWA";
include('../../includes/header.php');

// VARIABLES: Initialize
$output = '';
$error = '';
$name = '';

// LOGIC: Handle form submission with security level checks
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $name = $_GET['name'];
    
    switch ($_SESSION['security_level']) {
        case 0: // LOW - No protection
            $output = "Hello, " . $name;
            break;
            
        case 1: // MEDIUM - Basic script tag removal
            $name = str_replace('<script>', '', $name);
            $output = "Hello, " . $name;
            break;
            
        case 2: // HIGH - Full output encoding
            $output = "Hello, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            break;
    }
}

// UI: Vulnerability description
$description = '
<p>Cross-Site Scripting (XSS) attacks are a type of injection where malicious scripts are injected into otherwise benign and trusted websites.</p>
<p>This module demonstrates a simple reflected XSS vulnerability where user input is directly included in the page output without proper sanitization.</p>
';

// UI: Create the vulnerability content
$content = '
<div class="vulnerability-controls">
    <form method="GET" action="" class="mb-4">
        <!-- FORM: Input field for XSS testing -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="name" class="col-form-label">Enter your name:</label>
            </div>
            <div class="col-auto">
                <input type="text" id="name" name="name" class="form-control" value="' . htmlspecialchars($name) . '">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-themed">Submit</button>
            </div>
        </div>
    </form>
    
    <!-- SECURITY: Display current level -->
    <div class="mb-4">
        <h6>Security Level: ' . getSecurityLevelName() . '</h6>
    </div>
    
    ' . ($error ? '<div class="alert alert-danger">' . $error . '</div>' : '') . '
    
    <!-- RESULTS: Display output (vulnerable to XSS) -->
    <div class="result-container">
        <h6>Results:</h6>
        ' . $output . '
    </div>
    
    <!-- EDUCATION: Show source code examples -->
    <div class="mt-4">
        <h6>Source Code:</h6>
        <div class="code-block">
            <div class="code-header">
                <span>XSS Vulnerability Example</span>
            </div>
            <pre><code>' . getXssSourceCode($_SESSION['security_level']) . '</code></pre>
        </div>
    </div>
</div>';

// Display the vulnerability template
displayVulnerabilityTemplate('Cross-Site Scripting (XSS)', $description, getSecurityLevelName(), $content);

include('../../includes/footer.php');

// HELPER: Function to get appropriate source code example
function getXssSourceCode($level) {
    switch ($level) {
        case 0: // LOW
            return htmlspecialchars('<?php
// LOW: Vulnerable to XSS
$name = $_GET[\'name\'];
echo "Hello, " . $name;
?>');
        case 1: // MEDIUM
            return htmlspecialchars('<?php
// MEDIUM: Basic filtering but still vulnerable
$name = str_replace(\'<script>\', \'\', $_GET[\'name\']);
echo "Hello, " . $name;
?>');
        case 2: // HIGH
            return htmlspecialchars('<?php
// HIGH: Using output encoding
$name = $_GET[\'name\'];
echo "Hello, " . htmlspecialchars($name, ENT_QUOTES, \'UTF-8\');
?>');
    }
}
?>