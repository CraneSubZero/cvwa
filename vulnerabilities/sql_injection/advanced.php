<?php
// FILE: vulnerabilities/sql_injection/advanced.php
// PURPOSE: Demonstrate blind SQL injection

require_once '../../includes/database.php';
require_once '../../includes/header.php';

$id = $_GET['id'] ?? 1;
$message = '';

// Vulnerable query
$query = "SELECT username FROM users WHERE id = $id";
$result = $conn->query($query);
$user = $result->fetch(PDO::FETCH_ASSOC);

if($user) {
    $message = "User exists: " . htmlspecialchars($user['username']);
} else {
    $message = "No user found with ID: $id";
}
?>

<div class="container">
    <h2>Blind SQL Injection Demo</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4>User Lookup (Vulnerable)</h4>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="form-group">
                    <label>User ID:</label>
                    <input type="text" name="id" class="form-control" 
                           value="<?= htmlspecialchars($id) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Lookup</button>
            </form>
            
            <div class="mt-3 alert alert-info">
                <?= $message ?>
            </div>
            
            <div class="mt-4">
                <h5>Attack Examples:</h5>
                <p>Test for true condition:</p>
                <code>1 AND 1=1</code>
                
                <p class="mt-2">Test for false condition:</p>
                <code>1 AND 1=2</code>
                
                <p class="mt-2">Extract database version:</p>
                <code>1 AND SUBSTRING(@@version,1,1)='8'</code>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>