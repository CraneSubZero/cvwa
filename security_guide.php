<?php
// FILE: security_guide.php
// PURPOSE: Comprehensive security reference guide

declare(strict_types=1);

require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Please login to access the security guide');
}

$pageTitle = "Security Guide - CVWA";
include 'includes/header.php';

// Tab navigation
$tabs = [
    'sql' => 'SQL Injection',
    'xss' => 'XSS',
    'csrf' => 'CSRF',
    'file' => 'File Upload',
    'command' => 'Command Injection',
    'crypto' => 'Cryptography'
];
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-shield-alt"></i> Security Guide</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="guideTabs" role="tablist">
                        <?php foreach ($tabs as $id => $title): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $id === 'sql' ? 'active' : '' ?>" 
                                        id="<?= $id ?>-tab" data-bs-toggle="tab" 
                                        data-bs-target="#<?= $id ?>" type="button">
                                    <?= $title ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="tab-content" id="guideTabsContent">
                        <!-- SQL Injection Tab -->
                        <div class="tab-pane fade show active" id="sql" role="tabpanel">
                            <?php include 'guides/sql_guide.php'; ?>
                        </div>
                        
                        <!-- XSS Tab -->
                        <div class="tab-pane fade" id="xss" role="tabpanel">
                            <?php include 'guides/xss_guide.php'; ?>
                        </div>
                        
                        <!-- CSRF Tab -->
                        <div class="tab-pane fade" id="csrf" role="tabpanel">
                            <?php include 'guides/csrf_guide.php'; ?>
                        </div>
                        
                        <!-- File Upload Tab -->
                        <div class="tab-pane fade" id="file" role="tabpanel">
                            <?php include 'guides/file_guide.php'; ?>
                        </div>
                        
                        <!-- Command Injection Tab -->
                        <div class="tab-pane fade" id="command" role="tabpanel">
                            <?php include 'guides/command_guide.php'; ?>
                        </div>
                        
                        <!-- Cryptography Tab -->
                        <div class="tab-pane fade" id="crypto" role="tabpanel">
                            <?php include 'guides/crypto_guide.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>