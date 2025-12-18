<x-main-layout>
    @section('title', 'Statistik Mahasiswa')

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-6 text-primary fw-bold">
                    <i class="icon ni ni-users me-2"></i>Statistik Mahasiswa
                </h1>
                <p class="text-muted">Analisis data mahasiswa berdasarkan Fakultas</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-primary hover-lift">
                <i class="icon ni ni-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>

        <!-- Summary Cards -->
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
                                <h2 class="text-white mb-0">{{ $statistics->sum('total_students') }}</h2>
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
                                <i class="icon ni ni-book text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Total SKS Keseluruhan</h6>
                                <h2 class="text-white mb-0">{{ number_format($statistics->sum('total_sks'), 0) }}</h2>
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
                                <i class="icon ni ni-check-circle text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Mahasiswa Aktif</h6>
                                <h2 class="text-white mb-0">{{ $statistics->sum('active_students') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title text-primary mb-0">
                    <i class="icon ni ni-grid-alt me-2"></i>Detail Statistik per Program Studi
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">Program Studi</th>
                                <th class="py-3 text-center">SKS Lulus</th>
                                <th class="py-3 text-center">Jumlah Mahasiswa</th>
                                <th class="py-3 text-center">Mahasiswa Aktif</th>
                                <th class="py-3">Distribusi Jenjang</th>
                                <th class="py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics as $stat)
                            <tr>
                                <td class="fw-bold text-primary">{{ $stat['department']->nama }}</td>
                                <td class="text-center" data-bs-toggle="tooltip"
                                    title="Total SKS kurikulum program studi">
                                    {{ number_format($stat['total_sks'], 0) }}
                                </td>
                                <td class="text-center">{{ $stat['total_students'] }}</td>
                                <td class="text-center">{{ $stat['active_students'] }}</td>
                                <td>
                                    @foreach($stat['jenjang_distribution'] as $jenjang => $count)
                                    <span class="badge bg-primary me-1">{{ $jenjang }}: {{ $count }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dekan.department.student-details', $stat['department']->id) }}"
                                        class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top"
                                        title="Lihat Detail">
                                        <i class="icon ni ni-eye"></i>
                                    </a>
                                </td>
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

        .hover-shadow-lg:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1) !important;
        }

        /* Tambahkan tooltip */
        [data-bs-toggle="tooltip"] {
            cursor: pointer;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
    </script>
    @endpush
</x-main-layout>
