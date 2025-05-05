</div> <!-- Close main-content -->

<footer class="footer mt-5 py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; <?php echo date('Y'); ?> CVWA - College Vulnerable Web Application</p>
                <p class="small">Created for educational purposes only</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="small">Inspired by DVWA - Damn Vulnerable Web Application</p>
                <p class="small">Not for production use</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Theme Toggle Script -->
<script>
document.getElementById('theme-toggle').addEventListener('click', function() {
    const body = document.body;
    const themeIcon = this.querySelector('i');
    
    if (body.classList.contains('light-mode')) {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        document.cookie = "theme=dark; path=/; max-age=31536000"; // 1 year
    } else {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        document.cookie = "theme=light; path=/; max-age=31536000"; // 1 year
    }
});
</script>
</body>
</html>