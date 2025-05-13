<?php
// FILE: vulnerabilities/sql_injection/index.php
// PURPOSE: Demonstrate SQL injection vulnerabilities

require_once '../../includes/database.php';
require_once '../../includes/header.php';

$search = $_GET['search'] ?? '';
$results = [];
$query = "SELECT * FROM users WHERE username LIKE '%$search%'";

try {
    $stmt = $conn->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<div class="container">
    <h2>User Search (Vulnerable)</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4>Vulnerable Search Form</h4>
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
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3">Error: <?= $error ?></div>
            <?php endif; ?>
            
            <div class="mt-4">
                <h5>Executed Query:</h5>
                <code><?= htmlspecialchars($query) ?></code>
            </div>
        </div>
    </div>
    
    <?php if(!empty($results)): ?>
        <div class="card">
            <div class="card-header">
                <h4>Search Results (<?= count($results) ?>)</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= $row['is_admin'] ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card mt-4">
        <div class="card-header bg-warning">
            <h4>Attack Examples</h4>
        </div>
        <div class="card-body">
            <h5>Basic Injection:</h5>
            <code>' OR '1'='1</code>
            
            <h5 class="mt-3">Extract All Data:</h5>
            <code>' OR 1=1 -- </code>
            
            <h5 class="mt-3">Database Version:</h5>
            <code>' UNION SELECT 1,version(),3,4 -- </code>
            
            <h5 class="mt-3">Drop Table:</h5>
            <code>'; DROP TABLE users; -- </code>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>