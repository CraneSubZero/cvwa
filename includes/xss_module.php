<?php
session_start();
include('../../includes/config.php');
include('../../includes/functions.php');

if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "XSS - CVWA";
include('../../includes/header.php');

// Initialize variables
$output = '';
$error = '';
$name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $name = $_GET['name'];
    
    switch ($_SESSION['security_level']) {
        case 0: // LOW
            $output = "Hello, " . $name;
            break;
            
        case 1: // MEDIUM
            $name = str_replace('<script>', '', $name);
            $output = "Hello, " . $name;
            break;
            
        case 2: // HIGH
            $output = "Hello, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            break;
    }
}

// Vulnerability description
$description = '
<p>Cross-Site Scripting (XSS) attacks are a type of injection where malicious scripts are injected into otherwise benign and trusted websites.</p>
<p>This module demonstrates a simple reflected XSS vulnerability where user input is directly included in the page output without proper sanitization.</p>
<p><strong>Hints:</strong></p>
<ul>
    <li>Try simple alert payloads like <code>&lt;script&gt;alert(1)&lt;/script&gt;</code></li>
    <li>Experiment with different HTML tags that can execute JavaScript</li>
    <li>Try cookie stealing payloads when in low security mode</li>
</ul>
';

// Create the vulnerability content
$content = '
<div class="vulnerability-controls">
    <form method="GET" action="" class="mb-4">
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
    
    <div class="mb-4">
        <h6>Security Level: ' . getSecurityLevelName() . '</h6>
    </div>
    
    ' . ($error ? '<div class="alert alert-danger">' . $error . '</div>' : '') . '
    
    <div class="result-container">
        <h6>Results:</h6>
        ' . $output . '
    </div>
    
    <div class="mt-4">
        <h6>Source Code:</h6>
        <div class="code-block">
            <div class="code-header">
                <span>XSS Vulnerability Example</span>
            </div>
            <pre><code>';

switch ($_SESSION['security_level']) {
    case 0: // LOW
        $content .= htmlspecialchars('<?php
// LOW security level - Vulnerable to XSS
$name = $_GET[\'name\'];
echo "Hello, " . $name;
?>');
        break;
        
    case 1: // MEDIUM
        $content .= htmlspecialchars('<?php
// MEDIUM security level - Basic filtering but still vulnerable
$name = str_replace(\'<script>\', \'\', $_GET[\'name\']);
echo "Hello, " . $name;
?>');
        break;
        
    case 2: // HIGH
        $content .= htmlspecialchars('<?php
// HIGH security level - Using output encoding
$name = $_GET[\'name\'];
echo "Hello, " . htmlspecialchars($name, ENT_QUOTES, \'UTF-8\');
?>');
        break;
}

$content .= '</code></pre>
        </div>
    </div>
</div>';

// Display the vulnerability
displayVulnerabilityTemplate('Cross-Site Scripting (XSS)', $description, getSecurityLevelName(), $content);

include('../../includes/footer.php');
?>