<?php
// FILE: guides/sql_guide.php
// PURPOSE: SQL Injection educational content
?>
<div class="security-guide-content">
    <h3>SQL Injection</h3>
    <p>SQL injection is a code injection technique that might destroy your database.</p>
    
    <h4>Examples:</h4>
    <pre>SELECT * FROM users WHERE username = '' OR '1'='1' -- AND password = ''</pre>
    
    <h4>Prevention:</h4>
    <ul>
        <li>Use prepared statements with parameterized queries</li>
        <li>Use stored procedures</li>
        <li>Validate and sanitize input</li>
        <li>Implement least privilege</li>
    </ul>
    
    <h4>Vulnerable Code Example:</h4>
    <pre>
    // UNSAFE
    $query = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
    </pre>
    
    <h4>Secure Code Example:</h4>
    <pre>
    // SAFE (using prepared statements)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    </pre>
</div>