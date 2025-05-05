<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'CVWA - College Vulnerable Web Application'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>assets/css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : 'light-mode'; ?>">

<nav class="navbar navbar-expand-lg navbar-themed">
    <div class="container">
        <a class="navbar-brand" href="<?php echo getBaseUrl(); ?>">
            <i class="fas fa-shield-alt"></i> CVWA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>">Home</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Vulnerabilities
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>vulnerabilities/sql_injection/">SQL Injection</a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>vulnerabilities/xss/">Cross-Site Scripting</a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>vulnerabilities/csrf/">CSRF</a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>vulnerabilities/file_upload/">File Upload</a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>vulnerabilities/command_injection/">Command Injection</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>security_guide.php">Security Guide</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>about.php">About</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button id="theme-toggle" class="btn btn-sm btn-themed">
                        <i class="fas <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'fa-sun' : 'fa-moon'; ?>"></i>
                    </button>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="main-content">