    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        // Global Theme Toggle Logic
        var themeToggleBtn = document.getElementById('global-theme-toggle');
        if(themeToggleBtn) {
            var darkIcon = document.getElementById('theme-toggle-dark-icon');
            var lightIcon = document.getElementById('theme-toggle-light-icon');
            if (document.documentElement.classList.contains('dark')) { lightIcon.classList.remove('hidden'); } else { darkIcon.classList.remove('hidden'); }
            themeToggleBtn.addEventListener('click', function() {
                darkIcon.classList.toggle('hidden'); lightIcon.classList.toggle('hidden');
                if (localStorage.getItem('color-theme') === 'light' || (!localStorage.getItem('color-theme') && !document.documentElement.classList.contains('dark'))) {
                    document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light');
                }
            });
        }
    </script>
</body>
</html>    

