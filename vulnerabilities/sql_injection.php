<?php
// FILE: vulnerabilities/sql_injection/index.php
// PURPOSE: Demonstrates SQL injection with three security levels

declare(strict_types=1);

require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../../login.php', 'Please login to access this page');
}

$pageTitle = "SQL Injection - CVWA";
include '../../includes/header.php';

// Initialize variables
$user_id = $_GET['user_id'] ?? '';
$result = null;
$query_used = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($user_id)) {
    try {
        switch ($_SESSION['security_level']) {
            case 0: // LOW - Direct concatenation
                $query = "SELECT id, username, password FROM users WHERE id = $user_id";
                $query_used = $query;
                $result = $conn->query($query);
                if (!$result) {
                    throw new Exception($conn->error);
                }
                break;

            case 1: // MEDIUM - Basic filtering
                $user_id = str_replace(["'", '"', ';'], '', $user_id);
                $query = "SELECT id, username, password FROM users WHERE id = $user_id";
                $query_used = $query;
                $result = $conn->query($query);
                if (!$result) {
                    throw new Exception($conn->error);
                }
                break;

            case 2: // HIGH - Prepared statement
                if (!is_numeric($user_id)) {
                    throw new Exception('User ID must be numeric');
                }
                $query = "SELECT id, username FROM users WHERE id = ?";
                $query_used = "Prepared: SELECT id, username FROM users WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Vulnerability description
$description = <<<HTML
<div class="alert alert-info">
    <strong>Current Security Level:</strong> {$_SESSION['security_level']} - {getSecurityLevelName()}
</div>

<p>SQL Injection occurs when user input is improperly sanitized before being used in SQL queries, 
allowing attackers to manipulate database queries.</p>

<h5 class="mt-3">Testing Techniques:</h5>
<ul>
    <li><code>1 OR 1=1</code> - Retrieve all records</li>
    <li><code>1 UNION SELECT 1,table_name,3 FROM information_schema.tables</code> - List tables</li>
    <li><code>1; DROP TABLE users</code> - Dangerous destructive query</li>
</ul>

<h5 class="mt-3">Prevention Methods:</h5>
<ul>
    <li>Use prepared statements with parameterized queries</li>
    <li>Implement strict input validation</li>
    <li>Follow the principle of least privilege</li>
    <li>Use ORM frameworks</li>
</ul>
HTML;

// Build the content
$content = <<<HTML
<form method="GET" class="mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-md-4">
            <label for="user_id" class="form-label">User ID:</label>
            <input type="text" id="user_id" name="user_id" class="form-control" 
                   value="{$user_id}" placeholder="e.g. 1 OR 1=1">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-themed mt-md-4">Execute Query</button>
        </div>
    </div>
</form>

<div class="mb-4">
    <h5>Executed Query:</h5>
    <div class="code-block bg-light p-3">
        <code>{htmlspecialchars($query_used)}</code>
    </div>
</div>
HTML;

// Add error message if exists
if ($error) {
    $content .= <<<HTML
    <div class="alert alert-danger">
        <strong>Error:</strong> {$error}
    </div>
    HTML;
}

// Display results if available
if ($result) {
    $content .= <<<HTML
    <div class="result-container">
        <h5>Results:</h5>
        {$renderResults($result, $_SESSION['security_level'])}
    </div>
    HTML;
}

// Source code examples
$content .= <<<HTML
<div class="mt-4">
    <h5>Source Code Examples:</h5>
    <div class="accordion" id="sourceCodeAccordion">
        {$renderSourceCodeExamples($_SESSION['security_level'])}
    </div>
</div>
HTML;

// Display the vulnerability template
displayVulnerabilityTemplate('SQL Injection', $description, getSecurityLevelName(), $content);

include '../../includes/footer.php';

/**
 * Render query results
 */
function renderResults(mysqli_result $result, int $securityLevel): string {
    $output = '<div class="table-responsive"><table class="table table-striped">';
    
    // Table header
    $output .= '<thead><tr><th>ID</th><th>Username</th>';
    if ($securityLevel < 2) {
        $output .= '<th>Password</th>';
    }
    $output .= '</tr></thead><tbody>';
    
    // Table rows
    while ($row = $result->fetch_assoc()) {
        $output .= '<tr><td>' . htmlspecialchars($row['id']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['username']) . '</td>';
        if ($securityLevel < 2) {
            $output .= '<td>' . htmlspecialchars($row['password'] ?? '') . '</td>';
        }
        $output .= '</tr>';
    }
    
    $output .= '</tbody></table></div>';
    return $output;
}

/**
 * Render source code examples
 */
function renderSourceCodeExamples(int $securityLevel): string {
    $examples = [
        0 => [
            'title' => 'Low Security (Vulnerable)',
            'code' => <<<'PHP'
<?php
// Direct concatenation - EXTREMELY DANGEROUS
$user_id = $_GET['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
PHP
        ],
        1 => [
            'title' => 'Medium Security (Still Vulnerable)',
            'code' => <<<'PHP'
<?php
// Basic filtering - STILL VULNERABLE
$user_id = str_replace(["'", '"', ';'], '', $_GET['user_id']);
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
PHP
        ],
        2 => [
            'title' => 'High Security (Secure)',
            'code' => <<<'PHP'
<?php
// Prepared statement - SECURE
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $_GET['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
PHP
        ]
    ];
    
    $output = '';
    foreach ($examples as $level => $example) {
        $active = $level === $securityLevel ? 'show' : '';
        $output .= <<<HTML
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {$active ? '' : 'collapsed'}" 
                        type="button" data-bs-toggle="collapse" 
                        data-bs-target="#security{$level}">
                    {$example['title']}
                </button>
            </h2>
            <div id="security{$level}" class="accordion-collapse collapse {$active}"
                 data-bs-parent="#sourceCodeAccordion">
                <div class="accordion-body">
                    <pre><code class="language-php">{$example['code']}</code></pre>
                </div>
            </div>
        </div>
        HTML;
    }
    
    return $output;
}
?>