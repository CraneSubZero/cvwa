<?php
// FILE: vulnerabilities/sql_injection/secure.php
// PURPOSE: Demonstrate SQL injection prevention

require_once '../../includes/database.php';
require_once '../../includes/header.php';

$search = $_GET['search'] ?? '';
$results = [];
$query = "SELECT * FROM users WHERE username LIKE ?";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<div class="container">
    <h2>User Search (Secure)</h2>
    
    <div class="card mb-4 bg-success text-white">
        <div class="card-header">
            <h4>Secure Search Form (Using Prepared Statements)</h4>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="form-group">
                    <label>Search Users:</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            
            <div class="mt-4">
                <h5>Secure Query:</h5>
                <code>SELECT * FROM users WHERE username LIKE ?</code>
                <p class="mt-2">Parameter: <code><?= htmlspecialchars("%$search%") ?></code></p>
            </div>
        </div>
    </div>
    
    <!-- Results display same as vulnerable version -->
</div>

<?php require_once '../../includes/footer.php'; ?>