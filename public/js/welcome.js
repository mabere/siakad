document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.getElementById('navbar');
    const footer = document.getElementById('footer');
    const themeToggle = document.getElementById('theme-toggle');
    const navbarLogo = document.getElementById('navbar-logo');
    const footerLogo = document.getElementById('footer-logo');

    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        setDarkTheme();
    } else {
        setLightTheme();
    }

    themeToggle.addEventListener('click', function () {
        if (navbar.classList.contains('navbar-dark')) {
            setLightTheme();
            localStorage.setItem('theme', 'light');
        } else {
            setDarkTheme();
            localStorage.setItem('theme', 'dark');
        }
    });

    function setLightTheme() {
        navbar.classList.remove('navbar-dark', 'bg-primary');
        navbar.classList.add('navbar-light', 'bg-light');
        footer.classList.remove('bg-dark', 'text-light');
        footer.classList.add('bg-light', 'text-dark');
        navbarLogo.src = '{{ asset('images/dark-logo.png') }}';
        footerLogo.src = '{{ asset('images/dark-logo.png') }}';
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.classList.remove('bg-dark', 'text-light');
            modal.classList.add('bg-light', 'text-dark');
        });
    }

    function setDarkTheme() {
        navbar.classList.remove('navbar-light', 'bg-light');
        navbar.classList.add('navbar-dark', 'bg-primary');
        footer.classList.remove('bg-light', 'text-dark');
        footer.classList.add('bg-dark', 'text-light');
        navbarLogo.src = '{{ asset('images/light-logo.png') }}';
        footerLogo.src = '{{ asset('images/light-logo.png') }}';
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.classList.remove('bg-light', 'text-dark');
            modal.classList.add('bg-dark', 'text-light');
        });
    }

    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({ behavior: "smooth" });
            }
        });
    });
});
