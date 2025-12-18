<x-main-layout>
    @section('title', 'Laporan Akademik')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-6 text-primary fw-bold">
                    <i class="icon ni ni-file-text me-2"></i>Laporan Akademik
                </h1>
                <p class="text-muted">Ringkasan akademik fakultas</p>
            </div>
            <a href="{{ route('dekan.department.student-statistics') }}" class="btn btn-light-primary hover-lift">
                <i class="icon ni ni-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-4">
                <div class="card border-0 bg-gradient-teal shadow-lg hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-lg bg-white-10 rounded-circle me-3">
                                <i class="icon ni ni-users text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Total Mahasiswa</h6>
                                <h2 class="text-white mb-0">{{ $totalStudents }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card border-0 bg-gradient-success shadow-lg hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-lg bg-white-10 rounded-circle me-3">
                                <i class="icon ni ni-check-circle text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Mahasiswa Aktif</h6>
                                <h2 class="text-white mb-0">{{ $activeStudents }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card border-0 bg-gradient-purple shadow-lg hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-lg bg-white-10 rounded-circle me-3">
                                <i class="icon ni ni-star text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Rata-rata IPK</h6>
                                <h2 class="text-white mb-0">{{ number_format($avgIpk, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light-primary">
                <h5 class="card-title text-primary mb-0">
                    <i class="icon ni ni-grid-alt me-2"></i>Detail Laporan
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Laporan ini mencakup data agregat dari semua departemen di fakultas. Rata-rata IPK
                    dihitung berdasarkan mahasiswa yang memiliki nilai di tahun akademik aktif.</p>
                <!-- Tambahkan tombol export jika diperlukan -->
                <a href="#" class="btn btn-primary mb-3">Export ke Excel</a>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --teal: #4cc9f0;
            --purple: #7209b7;
            --success: #00d28c;
        }

        .bg-gradient-teal {
            background: linear-gradient(45deg, var(--teal), var(--primary));
        }

        .bg-gradient-purple {
            background: linear-gradient(45deg, var(--purple), var(--primary));
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, var(--success), var(--primary));
        }

        .hover-shadow-lg:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</x-main-layout>