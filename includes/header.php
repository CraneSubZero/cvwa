<?php
// FILE: includes/header.php
// PURPOSE: Consistent page header with Reddit-inspired UI

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CVWA - College Vulnerable Web Application') ?></title>
    
    <!-- Reddit-inspired CSS Variables -->
    <style>
        :root {
            --light-bg: #DAE0E6;
            --light-content: #FFFFFF;
            --light-text: #1A1A1B;
            --light-link: #0079D3;
            --light-accent: #FF4500;
            
            --dark-bg: #1A1A1B;
            --dark-content: #272729;
            --dark-text: #D7DADC;
            --dark-link: #4FBCFF;
            --dark-accent: #FF4500;
        }
        
        .bg-themed {
            background-color: var(--bs-body-bg);
        }
        
        .text-themed {
            color: var(--bs-body-color);
        }
    </style>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fas fa-shield-alt me-2"></i>CVWA
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Home</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Vulnerabilities
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $vulnerabilities = [
                                'sql_injection' => 'SQL Injection',
                                'xss' => 'XSS',
                                'csrf' => 'CSRF',
                                'file_upload' => 'File Upload',
                                'command_injection' => 'Command Injection'
                            ];
                            
                            foreach ($vulnerabilities as $path => $name): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>vulnerabilities/<?= $path ?>/">
                                        <?= $name ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>security_guide.php">Security Guide</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item me-2">
                        <button id="theme-toggle" class="btn btn-sm btn-outline-light">
                            <i class="fas <?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
                        </button>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>login.php">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container flex-grow-1 mb-4">
        <?php displayFlashMessage(); ?>