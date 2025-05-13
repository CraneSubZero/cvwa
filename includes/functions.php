<?php
// FILE: includes/functions.php
// PURPOSE: Helper functions and utilities

declare(strict_types=1);

/**
 * Check if user is authenticated
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current security level name
 */
function getSecurityLevelName(): string {
    return match ($_SESSION['security_level'] ?? 0) {
        1 => 'Medium',
        2 => 'High',
        default => 'Low',
    };
}

/**
 * Change security level with validation
 */
function changeSecurityLevel(int $level): bool {
    if ($level >= 0 && $level <= 2) {
        $_SESSION['security_level'] = $level;
        return true;
    }
    return false;
}

/**
 * Sanitize input based on security level
 */
function sanitizeInput(string $input, string $type = 'string'): string {
    global $conn;
    
    return match ($_SESSION['security_level'] ?? 0) {
        0 => $input, // No sanitization
        1 => match ($type) {
            'sql' => $conn->real_escape_string($input),
            'html' => htmlspecialchars($input, ENT_QUOTES, 'UTF-8'),
            default => $input,
        },
        2 => match ($type) {
            'sql' => $conn->real_escape_string($input),
            'html' => htmlspecialchars($input, ENT_QUOTES, 'UTF-8'),
            'command' => escapeshellarg($input),
            default => filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        },
    };
}

// ===== FIXED FUNCTION ===== (Original error was here)
/**
 * Log user activity (now accepts int|string for user ID)
 */
function logActivity($userId, string $action, string $details = ''): bool {
    global $conn;
    
    // Convert string IDs to integers (e.g., 'C' → 0 for guests)
    $userIdInt = is_numeric($userId) ? (int)$userId : 0;
    
    $stmt = $conn->prepare("INSERT INTO activity_logs 
        (user_id, action, details, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param("issss", 
        $userIdInt,  // Now ensures an integer is passed to MySQL
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    );
    
    return $stmt->execute();
}

/**
 * Display vulnerability template
 */
function displayVulnerabilityTemplate(
    string $title, 
    string $description, 
    string $difficulty, 
    string $content
): void {
    $difficultyClass = match (strtolower($difficulty)) {
        'medium' => 'difficulty-medium',
        'high' => 'difficulty-high',
        default => 'difficulty-low',
    };
    
    echo <<<HTML
    <div class="vulnerability-container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>$title</h4>
                <span class="difficulty-level $difficultyClass">$difficulty</span>
            </div>
            <div class="card-body">
                <div class="vulnerability-description">
                    $description
                </div>
                <hr>
                <div class="vulnerability-content">
                    $content
                </div>
            </div>
        </div>
    </div>
    HTML;
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect with message
 */
function redirect(string $location, string $message = '', string $type = 'success'): void {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $location");
    exit();
}

/**
 * Display flash message
 */
function displayFlashMessage(): void {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        echo '<div class="alert alert-' . htmlspecialchars($type) . '">' 
           . htmlspecialchars($_SESSION['flash_message']) 
           . '</div>';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}
?>