<?php
// FILE: vulnerabilities/sql_injection/index.php
// PURPOSE: Demonstrates SQL injection vulnerability with three security levels

session_start();
include('../../includes/config.php');
include('../../includes/functions.php');

// SECURITY: Check authentication
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "SQL Injection - CVWA";
include('../../includes/header.php');

// VARIABLES: Initialize
$user_id = '';
$result = null;
$query_used = '';
$error = '';

// LOGIC: Handle form submission with security level checks
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    switch ($_SESSION['security_level']) {
        case 0: // LOW - Direct concatenation (vulnerable)
            $query = "SELECT * FROM users WHERE id = $user_id";
            $query_used = htmlspecialchars($query);
            $result = mysqli_query($conn, $query);
            if (!$result) {
                $error = "Error in query: " . mysqli_error($conn);
            }
            break;
            
        case 1: // MEDIUM - Basic quote removal (still vulnerable)
            $user_id = str_replace(array("'", "\""), "", $user_id);
            $query = "SELECT * FROM users WHERE id = $user_id";
            $query_used = htmlspecialchars($query);
            $result = mysqli_query($conn, $query);
            if (!$result) {
                $error = "Error in query: " . mysqli_error($conn);
            }
            break;
            
        case 2: // HIGH - Prepared statements (secure)
            if (is_numeric($user_id)) {
                $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                $query_used = "Prepared statement: SELECT * FROM users WHERE id = ?";
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (!$result) {
                    $error = "Error in query: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Invalid input: User ID must be numeric";
            }
            break;
    }
}

// UI: Vulnerability description
$description = '
<p>SQL Injection is a code injection technique that exploits vulnerabilities in applications that build SQL queries using user-supplied input.</p>
<p>This module demonstrates SQL injection vulnerability where user input is directly incorporated into an SQL query.</p>
';

// UI: Create the vulnerability content
$content = '
<div class="vulnerability-controls">
    <form method="GET" action="" class="mb-4">
        <!-- FORM: Input field for SQLi testing -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="user_id" class="col-form-label">Enter User ID:</label>
            </div>
            <div class="col-auto">
                <input type="text" id="user_id" name="user_id" class="form-control" value="' . htmlspecialchars($user_id) . '">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-themed">Submit</button>
            </div>
        </div>
    </form>
    
    <!-- SECURITY: Display current level -->
    <div class="mb-4">
        <h6>Security Level: ' . getSecurityLevelName() . '</h6>
        ' . ($query_used ? '<div class="mt-2"><strong>Query executed:</strong> <code>' . $query_used . '</code></div>' : '') . '
    </div>
    
    ' . ($error ? '<div class="alert alert-danger">' . $error . '</div>' : '') . '
    
    <!-- RESULTS: Display query results -->
    <div class="result-container">
        <h6>Results:</h6>
        ' . getSqlResults($result, $_SESSION['security_level']) . '
    </div>
    
    <!-- EDUCATION: Show source code examples -->
    <div class="mt-4">
        <h6>Source Code:</h6>
        <div class="code-block">
            <div class="code-header">
                <span>SQL Injection Vulnerability Example</span>
            </div>
            <pre><code>' . getSqlSourceCode($_SESSION['security_level']) . '</code></pre>
        </div>
    </div>
</div>';

// Display the vulnerability template
displayVulnerabilityTemplate('SQL Injection', $description, getSecurityLevelName(), $content);

include('../../includes/footer.php');

// HELPER: Function to format SQL results
function getSqlResults($result, $security_level) {
    if (!$result) return '';
    
    if (mysqli_num_rows($result) > 0) {
        $output = '
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>';
        
        // Don't show password in HIGH security level
        if ($security_level < 2) {
            $output .= '<th>Password</th>';
        }
        
        $output .= '</tr></thead><tbody>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= '<tr>
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['username']) . '</td>';
            
            if ($security_level < 2) {
                $output .= '<td>' . htmlspecialchars($row['password']) . '</td>';
            }
            
            $output .= '</tr>';
        }
        
        $output .= '</tbody></table></div>';
        return $output;
    } else {
        return '<div class="alert alert-info">No results found.</div>';
    }
}

// HELPER: Function to get appropriate source code example
function getSqlSourceCode($level) {
    switch ($level) {
        case 0: // LOW
            return htmlspecialchars('<?php
// LOW: Vulnerable to SQL Injection
$user_id = $_GET[\'user_id\'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
?>');
        case 1: // MEDIUM
            return htmlspecialchars('<?php
// MEDIUM: Basic filtering but still vulnerable
$user_id = str_replace(array("\'", "\""), "", $_GET[\'user_id\']);
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
?>');
        case 2: // HIGH
            return htmlspecialchars('<?php
// HIGH: Using prepared statements
$user_id = $_GET[\'user_id\'];
if (is_numeric($user_id)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    $error = "Invalid input: User ID must be numeric";
}
?>');
    }
}
?>