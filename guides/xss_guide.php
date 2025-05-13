<?php
// FILE: guides/xss_guide.php
// PURPOSE: Cross-Site Scripting educational content
?>
<div class="security-guide-content">
    <h3>Cross-Site Scripting (XSS)</h3>
    <p>XSS enables attackers to inject client-side scripts into web pages.</p>
    
    <h4>Examples:</h4>
    <pre>&lt;script&gt;alert('XSS');&lt;/script&gt;</pre>
    
    <h4>Prevention:</h4>
    <ul>
        <li>Use htmlspecialchars() for output</li>
        <li>Implement Content Security Policy (CSP)</li>
        <li>Validate and sanitize input</li>
    </ul>
</div>