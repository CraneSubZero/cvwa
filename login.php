<?php
// FILE: login.php
// PURPOSE: Secure login system with security level awareness

declare(strict_types=1);

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Initialize security level if not set
if (!isset($_SESSION['security_level'])) {
    $_SESSION['security_level'] = 0;
}

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$username = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            // Security level based authentication
            switch ($_SESSION['security_level']) {
                case 0: // LOW - Vulnerable
                    $query = "SELECT id, username FROM users WHERE username = '$username' AND password = '$password'";
                    $result = $conn->query($query);
                    break;
                    
                case 1: // MEDIUM - Basic protection
                    $username = $conn->real_escape_string($username);
                    $password = $conn->real_escape_string($password);
                    $query = "SELECT id, username FROM users WHERE username = '$username' AND password = '$password'";
                    $result = $conn->query($query);
                    break;
                    
                case 2: // HIGH - Secure
                    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    break;
            }
            
            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // For HIGH security, verify password hash
                if ($_SESSION['security_level'] === 2) {
                    if (!password_verify($password, $user['password'])) {
                        throw new Exception('Invalid credentials');
                    }
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                logActivity($user['id'], 'login', 'Successful login');
                redirect('index.php', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
            } else {
                throw new Exception('Invalid username or password');
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            logActivity(0, 'login_fail', "Failed login attempt for $username");
        }
    }
}

$pageTitle = "Login - CVWA";
include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login</h4>
            </div>
            
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($username) ?>" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            
            <div class="card-footer text-center">
                <p class="mb-0 small">
                    Default credentials: <strong>admin</strong> / <strong>admin</strong>
                </p>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>register.php" class="text-muted small">Create an account</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>