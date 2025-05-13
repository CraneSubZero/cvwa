<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

// FILE: register.php
// IMPROVED: Added proper structure and security

// Check if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        // SECURITY: Different handling per security level
        switch ($_SESSION['security_level'] ?? 0) {
            case 0: // LOW - No protection
                $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
                break;
                
            case 1: // MEDIUM - Basic sanitization
                $username = mysqli_real_escape_string($conn, $username);
                $password = mysqli_real_escape_string($conn, $password);
                $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
                break;
                
            case 2: // HIGH - Prepared statement
                $query = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ss", $username, $password);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;
        }
        
        if (isset($query) && $_SESSION['security_level'] < 2) {
            mysqli_query($conn, $query);
        }
        
        header('Location: login.php');
        exit();
    }
}

$pageTitle = "Register - CVWA";
include('includes/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-themed">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>