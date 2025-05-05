<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$success_msg = '';
$error_msg = '';

// Process security level change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['security_level'])) {
    $level = (int)$_POST['security_level'];
    
    if (changeSecurityLevel($level)) {
        $success_msg = 'Security level changed to ' . getSecurityLevelName() . '.';
        
        // Log the security level change
        logActivity($_SESSION['user_id'], 'security_change', 'Changed security level to ' . getSecurityLevelName());
    } else {
        $error_msg = 'Invalid security level.';
    }
}

$pageTitle = "Security Level - CVWA";
include('includes/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Security Level Settings</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>
                    
                    <p>The security level determines how vulnerable the application is. This allows you to learn about different types of vulnerabilities and exploitation techniques.</p>
                    
                    <div class="alert alert-info">
                        <strong>Current Security Level:</strong> <?php echo getSecurityLevelName(); ?>
                    </div>
                    
                    <form method="POST" action="security.php">
                        <div class="mb-3">
                            <label for="security_level" class="form-label">Change Security Level:</label>
                            <select class="form-select" id="security_level" name="security_level">
                                <option value="0" <?php echo ($_SESSION['security_level'] == 0) ? 'selected' : ''; ?>>Low (Most Vulnerable)</option>
                                <option value="1" <?php echo ($_SESSION['security_level'] == 1) ? 'selected' : ''; ?>>Medium (Somewhat Vulnerable)</option>
                                <option value="2" <?php echo ($_SESSION['security_level'] == 2) ? 'selected' : ''; ?>>High (More Secure)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-themed">Submit</button>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <h5>Level Descriptions:</h5>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-danger text-white">
                                Low
                            </div>
                            <div class="card-body">
                                <p>This security level is completely vulnerable and has no security measures implemented. It's designed to be easy to exploit, making it suitable for beginners to learn the basics of web application vulnerabilities.</p>
                                <p><strong>Features:</strong> No input validation, no sanitization, plain text passwords, etc.</p>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark">
                                Medium
                            </div>
                            <div class="card-body">
                                <p>This security level has some security controls but they are flawed and can be bypassed. It represents a more realistic scenario that you might encounter in the wild.</p>
                                <p><strong>Features:</strong> Basic input sanitization, some CSRF protection, etc.</p>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                High
                            </div>
                            <div class="card-body">
                                <p>This security level implements more comprehensive security controls, making it more challenging to exploit. However, for educational purposes, it still contains vulnerabilities - just more subtle ones.</p>
                                <p><strong>Features:</strong> More thorough input validation, output encoding, CSRF tokens, etc.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>