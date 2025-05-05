<?php
// FILE: includes/footer.php
// PURPOSE: Consistent page footer with theme toggle

declare(strict_types=1);
?>
    </main>

    <footer class="footer mt-auto py-3 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">&copy; <?= date('Y') ?> CVWA - College Vulnerable Web Application</p>
                    <p class="small mb-0">Created for educational purposes only</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small mb-1">Inspired by DVWA - Damn Vulnerable Web Application</p>
                    <p class="small mb-0">Not for production use</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Toggle Script -->
    <script>
    document.getElementById('theme-toggle').addEventListener('click', function() {
        const htmlEl = document.documentElement;
        const isDark = htmlEl.getAttribute('data-bs-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';
        
        htmlEl.setAttribute('data-bs-theme', newTheme);
        this.querySelector('i').classList.toggle('fa-sun', !isDark);
        this.querySelector('i').classList.toggle('fa-moon', isDark);
        
        document.cookie = `theme=${newTheme}; path=/; max-age=${60*60*24*365}`;
    });
    </script>
</body>
</html>