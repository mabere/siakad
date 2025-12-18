<x-main-layout>
    @section('title', 'Detail Mahasiswa')

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-6 text-primary fw-bold">
                    <i class="icon ni ni-users me-2"></i>@yield('title') - {{ $department->nama }}
                </h1>
                <p class="text-muted">Statistik mahasiswa berdasarkan angkatan</p>
            </div>
            <a href="{{ route('dekan.department.student-statistics') }}" class="btn btn-primary hover-lift">
                <i class="icon ni ni-arrow-left me-2"></i>Statistik
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-4">
                <div class="card border-0 bg-gradient-teal shadow-lg hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-lg bg-white-10 rounded-circle me-3">
                                <i class="icon ni ni-book text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">SKS Lulus</h6>
                                <h2 class="text-white mb-0">{{ number_format($totalSks, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card border-0 bg-gradient-primary shadow-lg hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-lg bg-white-10 rounded-circle me-3">
                                <i class="icon ni ni-users text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Total Mahasiswa</h6>
                                <h2 class="text-white mb-0">{{ $studentStatsByYear->sum('total_students') }}</h2>
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
                                <h2 class="text-white mb-0">{{ $studentStatsByYear->sum('active_students') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <!-- Tambahkan section chart IPK setelah Chart Section yang ada -->
            <div class="col-md-6">
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-header bg-light-primary">
                        <h5 class="card-title text-primary mb-0">
                            <i class="icon ni ni-line-chart me-2"></i>Grafik Rata-rata IPK per Angkatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ipkChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="col-md-6">
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-header bg-light-primary">
                        <h5 class="card-title text-primary mb-0">
                            <i class="icon ni ni-bar-chart me-2"></i>Grafik Statistik per Angkatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="studentChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Detail Table -->
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title text-primary mb-0">
                    <i class="icon ni ni-grid-alt me-2"></i>Statistik Mahasiswa per Angkatan
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">Angkatan</th>
                                <th class="py-3 text-center">Jumlah Mahasiswa</th>
                                <th class="py-3 text-center">Mahasiswa Aktif</th>
                                <th class="py-3">Distribusi Jenjang</th>
                                <th class="py-3 text-center">Rata-rata IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentStatsByYear as $yearStats)
                            <tr>
                                <td class="fw-bold text-primary">{{ $yearStats['year'] }}</td>
                                <td class="text-center">{{ $yearStats['total_students'] }}</td>
                                <td class="text-center">{{ $yearStats['active_students'] }}</td>
                                <td>
                                    @foreach($yearStats['jenjang_distribution'] as $jenjang => $count)
                                    <span class="badge bg-primary me-1">{{ $jenjang }}: {{ $count }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">{{ isset($yearStats['average_ipk']) ? $yearStats['average_ipk']
                                    : '0.00' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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


    <!-- Modifikasi script section -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        // Chart Statistik Mahasiswa (yang sudah ada)
            const years = @json(array_keys($studentStatsByYear->toArray()));
            const totalStudents = @json($studentStatsByYear->pluck('total_students')->toArray());
            const activeStudents = @json($studentStatsByYear->pluck('active_students')->toArray());

            const ctx = document.getElementById('studentChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: years,
                    datasets: [
                        {
                            label: 'Jumlah Mahasiswa',
                            data: totalStudents,
                            backgroundColor: 'rgba(67, 97, 238, 0.7)',
                            borderColor: 'rgba(67, 97, 238, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Mahasiswa Aktif',
                            data: activeStudents,
                            backgroundColor: 'rgba(0, 210, 140, 0.7)',
                            borderColor: 'rgba(0, 210, 140, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Jumlah Mahasiswa' } },
                        x: { title: { display: true, text: 'Angkatan' } }
                    },
                    plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Chart IPK
            const ipkData = @json($studentStatsByYear->pluck('average_ipk')->toArray());
            const ipkCtx = document.getElementById('ipkChart').getContext('2d');
            new Chart(ipkCtx, {
                type: 'line',
                data: {
                    labels: years,
                    datasets: [{
                        label: 'Rata-rata IPK',
                        data: ipkData,
                        fill: false,
                        borderColor: 'rgb(114, 9, 183)',
                        backgroundColor: 'rgba(114, 9, 183, 0.5)',
                        tension: 0.1,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4,
                            title: { display: true, text: 'IPK' }
                        },
                        x: {
                            title: { display: true, text: 'Angkatan' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
    @endpush

</x-main-layout>
