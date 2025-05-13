<?php
// FILE: guides/command_guide.php
// PURPOSE: Command injection content
?>
<div class="security-guide-content">
    <h3>Command Injection</h3>
    <p>Allows attackers to execute arbitrary commands on the host OS.</p>
    
    <h4>Examples:</h4>
    <pre>; rm -rf /</pre>
    
    <h4>Prevention:</h4>
    <ul>
        <li>Use escapeshellarg()</li>
        <li>Avoid shell commands when possible</li>
        <li>Implement strict input validation</li>
    </ul>
</div>