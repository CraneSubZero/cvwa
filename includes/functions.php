<?php
/**
 * Helper functions for CVWA
 */

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current security level name
function getSecurityLevelName() {
    $level = isset($_SESSION['security_level']) ? $_SESSION['security_level'] : 0;
    
    switch ($level) {
        case 0:
            return 'Low';
        case 1:
            return 'Medium';
        case 2:
            return 'High';
        default:
            return 'Unknown';
    }
}

// Change security level
function changeSecurityLevel($level) {
    $level = (int)$level;
    
    if ($level >= 0 && $level <= 2) {
        $_SESSION['security_level'] = $level;
        return true;
    }
    
    return false;
}

// Input sanitization based on security level
function sanitizeInput($input, $type = 'string') {
    global $conn;
    $level = isset($_SESSION['security_level']) ? $_SESSION['security_level'] : 0;
    
    // Low level - No sanitization (intentionally vulnerable)
    if ($level == 0) {
        return $input;
    }
    
    // Medium level - Basic sanitization
    if ($level == 1) {
        if ($type == 'sql') {
            return mysqli_real_escape_string($conn, $input);
        } elseif ($type == 'html') {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }
    
    // High level - More thorough sanitization
    if ($level == 2) {
        if ($type == 'sql') {
            // Prepared statements would be used instead in actual code
            return mysqli_real_escape_string($conn, $input);
        } elseif ($type == 'html') {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        } elseif ($type == 'command') {
            return escapeshellarg($input);
        }
        
        // Default string sanitization
        return filter_var($input, FILTER_SANITIZE_STRING);
    }
    
    // Default fallback
    return $input;
}

// Log activity for tracking and analysis
function logActivity($user_id, $action, $details = '') {
    global $conn;
    
    $user_id = (int)$user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $details = mysqli_real_escape_string($conn, $details);
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);
    
    $query = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
              VALUES ($user_id, '$action', '$details', '$ip', '$user_agent', NOW())";
    
    mysqli_query($conn, $query);
}

// Generate token for CSRF protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}

// Check if file extension is allowed
function isAllowedFileExtension($filename, $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif']) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowed_extensions);
}

// Display a vulnerability template
function displayVulnerabilityTemplate($title, $description, $difficulty = 'low', $content = '') {
    // Get difficulty class
    $difficultyClass = '';
    switch (strtolower($difficulty)) {
        case 'low':
            $difficultyClass = 'difficulty-low';
            break;
        case 'medium':
            $difficultyClass = 'difficulty-medium';
            break;
        case 'high':
            $difficultyClass = 'difficulty-high';
            break;
    }
    
    echo '
    <div class="vulnerability-container">
        <div class="card">
            <div class="card-header">
                <h4>' . $title . ' <span class="difficulty-level ' . $difficultyClass . '">' . ucfirst($difficulty) . '</span></h4>
            </div>
            <div class="card-body">
                <div class="vulnerability-description">
                    ' . $description . '
                </div>
                <hr>
                <div class="vulnerability-content">
                    ' . $content . '
                </div>
            </div>
        </div>
    </div>';
}