<?php
// FILE: vulnerabilities/file_upload/index.php
// PURPOSE: Demonstrates file upload vulnerabilities

declare(strict_types=1);

require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../../login.php', 'Please login to access this page');
}

$pageTitle = "File Upload - CVWA";
include '../../includes/header.php';

$message = '';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

// Process file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $file = $_FILES['file'];
        
        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }
        
        $fileName = basename($file['name']);
        $targetDir = "../../uploads/";
        $targetPath = $targetDir . $fileName;
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Security level based handling
        switch ($_SESSION['security_level']) {
            case 0: // LOW - No protection
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $message = "File uploaded successfully to: " . htmlspecialchars($targetPath);
                } else {
                    throw new Exception("Failed to move uploaded file");
                }
                break;
                
            case 1: // MEDIUM - Basic extension check
                if (!in_array($fileExt, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG, JPEG, PNG, GIF allowed.");
                }
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $message = "File uploaded successfully!";
                } else {
                    throw new Exception("Failed to move uploaded file");
                }
                break;
                
            case 2: // HIGH - Secure upload
                if (!in_array($fileExt, $allowedExtensions)) {
                    throw new Exception("Invalid file type");
                }
                
                // Verify MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                $allowedMimes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif'
                ];
                
                if (!in_array($mime, $allowedMimes)) {
                    throw new Exception("Invalid file content");
                }
                
                // Generate safe filename
                $safeName = md5(uniqid()) . '.' . $fileExt;
                $safePath = $targetDir . $safeName;
                
                if (move_uploaded_file($file['tmp_name'], $safePath)) {
                    $message = "File uploaded securely with random name: " . htmlspecialchars($safeName);
                } else {
                    throw new Exception("Failed to move uploaded file");
                }
                break;
        }
        
        logActivity($_SESSION['user_id'], 'file_upload', "Uploaded $fileName");
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        logActivity($_SESSION['user_id'], 'file_upload_error', $e->getMessage());
    }
}

// Vulnerability description
$description = <<<HTML
<div class="alert alert-info">
    <strong>Current Security Level:</strong> {$_SESSION['security_level']} - {getSecurityLevelName()}
</div>

<p>File upload vulnerabilities occur when an application accepts file uploads without proper validation,
allowing attackers to upload malicious files that could compromise the system.</p>

<h5 class="mt-3">Testing Techniques:</h5>
<ul>
    <li>Upload PHP shell scripts (e.g., <code>.php</code> files)</li>
    <li>Try double extensions (e.g., <code>image.php.jpg</code>)</li>
    <li>Attempt path traversal in filenames</li>
</ul>
HTML;

// Build the content
$content = <<<HTML
<div class="card mb-4">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file" class="form-label">Select file to upload:</label>
                <input class="form-control" type="file" id="file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload File</button>
        </form>
        
        {$message ? "<div class='mt-3 alert alert-info'>$message</div>" : ""}
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Security Level Details</h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="securityDetails">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#lowSec">
                        Low Security (Vulnerable)
                    </button>
                </h2>
                <div id="lowSec" class="accordion-collapse collapse show" data-bs-parent="#securityDetails">
                    <div class="accordion-body">
                        <p>No file validation - accepts any file type with original filename.</p>
                        <pre><code>// Vulnerable code
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    // File uploaded
}</code></pre>
                    </div>
                </div>
            </div>
            
            <!-- Medium and High security details would follow similar structure -->
        </div>
    </div>
</div>
HTML;

displayVulnerabilityTemplate('File Upload', $description, getSecurityLevelName(), $content);
include '../../includes/footer.php';