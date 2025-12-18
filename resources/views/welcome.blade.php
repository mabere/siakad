<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD | Selamat Datang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/welcome.css">
</head>

<body>
    <header class="sticky-top">
        @php
        $navClass = $navClass ?? 'navbar-dark bg-primary';
        @endphp

        <nav class="navbar navbar-expand-lg {{ $navClass }}" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ str_contains($navClass, 'navbar-dark')
                        ? asset('images/light-logo.png')
                        : asset('images/dark-logo.png') }}" alt="Logo SIAKAD" class="logo-img" id="navbar-logo">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-lg-center">
                        <li class="nav-item me-1">
                            <a class="nav-link text-white {{ $navClass }}" href="/">Beranda</a>
                        </li>

                        <li class="nav-item ms-lg-2">
                            <button id="theme-toggle" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-moon"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1 py-3">
        <section id="hero" class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 order-1 order-md-1 mb-4 mb-md-0">
                        <div class="hero-content text-start p-4 rounded">
                            <h1 class="text-primary display-4 fw-bold">SISTEM INFORMASI AKADEMIK</h1>
                            <p class="fw-bold mb-4">Selamat datang di aplikasi Siakad Fakultas Keguruan dan Ilmu
                                Pendidikan</p>
                            <h5 class="fw-bold mb-4">Sistem Terintegrasi Manajemen Akademik Digital Fakultas Keguruan
                                dan Ilmu Pendidikan</h5>
                            <button class="btn btn-light btn-md" data-bs-toggle="modal"
                                data-bs-target="#loginModal">Mulai Sekarang</button>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 order-2 order-md-2">
                        <div id="fiturCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <!-- Carousel Item 1 -->
                                <div class="carousel-item active">
                                    <div class="card shadow mx-auto">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Pengisian KRS</h5>
                                            <p class="card-text">Registrasi mata kuliah secara mudah dan cepat.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Carousel Item 2 -->
                                <div class="carousel-item">
                                    <div class="card shadow mx-auto">
                                        <div class="card-body text-center">
                                            <i class="fas fa-poll fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Lihat Nilai & Transkrip</h5>
                                            <p class="card-text">Cek nilai secara real-time dan dapatkan transkrip
                                                lengkap.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Carousel Item 3 -->
                                <div class="carousel-item">
                                    <div class="card shadow mx-auto">
                                        <div class="card-body text-center">
                                            <i class="fas fa-bell fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Notifikasi</h5>
                                            <p class="card-text">Dapatkan update dan pengumuman penting secara instan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Carousel Item 4 -->
                                <div class="carousel-item">
                                    <div class="card shadow mx-auto">
                                        <div class="card-body text-center">
                                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Keamanan Data</h5>
                                            <p class="card-text">Perlindungan data akademik dengan sistem keamanan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#fiturCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#fiturCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-3 {{ str_contains($navClass, 'navbar-dark') ? 'bg-primary text-light' : 'bg-light text-dark' }}"
        id="footer">
        <div class="container">
            <div class="row mb-2">
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <img src="{{ str_contains($navClass, 'navbar-dark')
                        ? asset('images/light-logo.png')
                        : asset('images/dark-logo.png') }}" alt="Logo SIAKAD" class="logo-img mb-2" id="footer-logo">
                    <p>Aplikasi manajemen akademik terintegrasi digital Fakultas Keguruan dan Ilmu Pendidikan.</p>
                </div>
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h6>Menu Utama</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="tel:+6284082124777" class="text-grey text-decoration-none">
                                <i class="fas fa-phone me-2"></i>(0408) 2421-777
                            </a>
                        </li>
                        <li>
                            <a href="mailto:info@fkip-unilaki.ac.id" class="text-grey text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>info@fkip-unilaki.ac.id</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-md-4">
                    <h6>Hubungi Kami</h6>
                    <address class="small">
                        Jl. Sultan Hasanuddin No. 234<br>
                        Unaaha, Sulawesi Tenggara<br>
                        93461
                    </address>

                </div>
            </div>
            <hr class="p-0">
            <div class="text-center mt-0">
                <small>© {{ date('Y') }} SIAKAD. Hak Cipta Dilindungi.
                    <a href="https://fkip-unilaki.ac.id" target="_blank" class="text-decoration-none"><strong>Fakultas
                            Keguruan dan Ilmu Pendidikan</strong></a>
                </small><br>
                <small>Dikembangkan oleh <a href="mailto:ramli.baharuddin@gmail.com"
                        class="text-decoration-none"><strong>Tim Pengembang</strong></a></small>
            </div>
        </div>
    </footer>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fs-3 text-primary" id="loginModalLabel">Akses Sistem Akademik</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <nav class="mb-4">
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-staff-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-staff" type="button" role="tab" aria-controls="nav-staff"
                                aria-selected="true">
                                <i class="fas fa-user-shield me-2"></i>Akademik
                            </button>
                            <button class="nav-link" id="nav-dosen-tab" data-bs-toggle="tab" data-bs-target="#nav-dosen"
                                type="button" role="tab" aria-controls="nav-dosen" aria-selected="false">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Dosen & Mahasiswa
                            </button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <!-- Akademik Tab -->
                        <div class="tab-pane fade show active" id="nav-staff" role="tabpanel"
                            aria-labelledby="nav-staff-tab">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <a href="/auth/kaprodi"
                                        class="card h-100 border-0 hover-shadow text-decoration-none">
                                        <div class="card-body text-center py-5">
                                            <div class="icon-wrapper bg-primary-soft mb-4">
                                                <i class="fas fa-user-tie fa-2x text-primary"></i>
                                            </div>
                                            <h3 class="h5 mb-2">Kaprodi</h3>
                                            <p class="text-muted small mb-0">Manajemen program studi dan kurikulum</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <span class="btn btn-primary w-100">Masuk</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4">
                                    <a href="/auth/dekan" class="card h-100 border-0 hover-shadow text-decoration-none">
                                        <div class="card-body text-center py-5">
                                            <div class="icon-wrapper bg-primary-soft mb-4">
                                                <i class="fas fa-university fa-2x text-primary"></i>
                                            </div>
                                            <h3 class="h5 mb-2">Dekan</h3>
                                            <p class="text-muted small mb-0">Pengawasan dan kebijakan fakultas</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <span class="btn btn-primary w-100">Masuk</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4">
                                    <a href="/auth/staff" class="card h-100 border-0 hover-shadow text-decoration-none">
                                        <div class="card-body text-center py-5">
                                            <div class="icon-wrapper bg-primary-soft mb-4">
                                                <i class="fas fa-users-cog fa-2x text-primary"></i>
                                            </div>
                                            <h3 class="h5 mb-2">Staff</h3>
                                            <p class="text-muted small mb-0">Operasional harian sistem akademik</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <span class="btn btn-primary w-100">Masuk</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Dosen & Mahasiswa Tab -->
                        <div class="tab-pane fade" id="nav-dosen" role="tabpanel" aria-labelledby="nav-dosen-tab">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <a href="/auth/dosen" class="card h-100 border-0 hover-shadow text-decoration-none">
                                        <div class="card-body text-center py-5">
                                            <div class="icon-wrapper bg-primary-soft mb-4">
                                                <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                                            </div>
                                            <h3 class="h5 mb-2">Dosen</h3>
                                            <p class="text-muted small mb-0">Input nilai dan manajemen perkuliahan</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <span class="btn btn-outline-primary w-100">Masuk</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-6">
                                    <a href="/auth/mahasiswa"
                                        class="card h-100 border-0 hover-shadow text-decoration-none">
                                        <div class="card-body text-center py-5">
                                            <div class="icon-wrapper bg-primary-soft mb-4">
                                                <i class="fas fa-user-graduate fa-2x text-primary"></i>
                                            </div>
                                            <h3 class="h5 mb-2">Mahasiswa</h3>
                                            <p class="text-muted small mb-0">KRS, nilai, dan informasi akademik</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <span class="btn btn-outline-primary w-100">Masuk</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center border-0 pt-0">
                    <p class="text-muted small mb-0">
                        Butuh bantuan?
                        <a href="mailto:info@fkip-unilaki.ac.id" class="text-decoration-none">Hubungi Helpdesk</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const navbar = document.getElementById('navbar');
            const footer = document.getElementById('footer');
            const heroSection = document.getElementById('hero');
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
                heroSection.classList.remove('hero-dark', 'text-light');
                heroSection.classList.add('hero-light', 'text-dark');
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
                heroSection.classList.remove('hero-light', 'text-dark');
                heroSection.classList.add('hero-dark', 'text-light');
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

    </script>
</body>

</html>
