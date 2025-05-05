<?php
session_start();
include('../../includes/config.php');
include('../../includes/functions.php');

if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "Stored XSS - CVWA";
include('../../includes/header.php');

// Initialize database table for messages if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS xss_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $query);

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    
    switch ($_SESSION['security_level']) {
        case 0: // LOW
            $query = "INSERT INTO xss_messages (message) VALUES ('$message')";
            break;
            
        case 1: // MEDIUM
            $message = str_replace('<script>', '', $message);
            $query = "INSERT INTO xss_messages (message) VALUES ('" . mysqli_real_escape_string($conn, $message) . "')";
            break;
            
        case 2: // HIGH
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
            $query = "INSERT INTO xss_messages (message) VALUES ('" . mysqli_real_escape_string($conn, $message) . "')";
            break;
    }
    
    mysqli_query($conn, $query);
}

// Get all messages
$messages = [];
$result = mysqli_query($conn, "SELECT * FROM xss_messages ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

// Vulnerability description
$description = '
<p>Stored Cross-Site Scripting (XSS) attacks are a type of injection where malicious scripts are permanently stored on the target server.</p>
<p>This module demonstrates a stored XSS vulnerability where user input is saved to a database and then displayed to other users without proper sanitization.</p>
<p><strong>Hints:</strong></p>
<ul>
    <li>Try simple alert payloads that will execute when the page loads</li>
    <li>Experiment with different HTML tags that can execute JavaScript</li>
    <li>Try cookie stealing payloads that will affect other users</li>
</ul>
';

// Create the vulnerability content
$content = '
<div class="vulnerability-controls">
    <form method="POST" action="" class="mb-4">
        <div class="mb-3">
            <label for="message" class="form-label">Leave a message:</label>
            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-themed">Submit</button>
    </form>
    
    <div class="mb-4">
        <h6>Security Level: ' . getSecurityLevelName() . '</h6>
    </div>
    
    <div class="result-container">
        <h6>Messages:</h6>
        <div class="list-group">';

foreach ($messages as $message) {
    if ($_SESSION['security_level'] < 2) {
        $content .= '<div class="list-group-item">' . $message['message'] . '</div>';
    } else {
        $content .= '<div class="list-group-item">' . htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

$content .= '
        </div>
    </div>
    
    <div class="mt-4">
        <h6>Source Code:</h6>
        <div class="code-block">
            <div class="code-header">
                <span>Stored XSS Vulnerability Example</span>
            </div>
            <pre><code>';

switch ($_SESSION['security_level']) {
    case 0: // LOW
        $content .= htmlspecialchars('<?php
// LOW security level - Vulnerable to Stored XSS
$message = $_POST[\'message\'];
$query = "INSERT INTO xss_messages (message) VALUES (\'$message\')";
mysqli_query($conn, $query);

// Displaying messages without sanitization
$messages = mysqli_query($conn, "SELECT * FROM xss_messages");
while ($row = mysqli_fetch_assoc($messages)) {
    echo $row[\'message\'];
}
?>');
        break;
        
    case 1: // MEDIUM
        $content .= htmlspecialchars('<?php
// MEDIUM security level - Basic filtering but still vulnerable
$message = str_replace(\'<script>\', \'\', $_POST[\'message\']);
$query = "INSERT INTO xss_messages (message) VALUES (\'" . mysqli_real_escape_string($conn, $message) . "\')";
mysqli_query($conn, $query);

// Displaying messages with minimal sanitization
$messages = mysqli_query($conn, "SELECT * FROM xss_messages");
while ($row = mysqli_fetch_assoc($messages)) {
    echo $row[\'message\'];
}
?>');
        break;
        
    case 2: // HIGH
        $content .= htmlspecialchars('<?php
// HIGH security level - Using proper sanitization
$message = htmlspecialchars($_POST[\'message\'], ENT_QUOTES, \'UTF-8\');
$query = "INSERT INTO xss_messages (message) VALUES (\'" . mysqli_real_escape_string($conn, $message) . "\')";
mysqli_query($conn, $query);

// Displaying messages with output encoding
$messages = mysqli_query($conn, "SELECT * FROM xss_messages");
while ($row = mysqli_fetch_assoc($messages)) {
    echo htmlspecialchars($row[\'message\'], ENT_QUOTES, \'UTF-8\');
}
?>');
        break;
}

$content .= '</code></pre>
        </div>
    </div>
</div>';

// Display the vulnerability
displayVulnerabilityTemplate('Stored XSS', $description, getSecurityLevelName(), $content);

include('../../includes/footer.php');
?>