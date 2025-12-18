<x-main-layout>
    @section('title', 'Dashboard EDOM Fakultas')

    <div class="nk-block">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Dashboard EDOM - Fakultas ID: {{ $facultyId }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <span class="text-muted">Diperbarui: {{ now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Fakultas -->
        <div class="row g-gs mb-4">
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-items-center mb-3">
                            <h6 class="card-title">
                                <em class="icon ni ni-chart-up me-2"></em> Ringkasan EDOM Fakultas
                            </h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="nk-ibx-stats">
                                    <div class="nk-ibx-stats-count">{{ $totalRespondents }}</div>
                                    <div class="nk-ibx-stats-text">Jumlah Respondens</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="nk-ibx-stats">
                                    <div class="nk-ibx-stats-count text-success">{{ $facultyAverageRating }}</div>
                                    <div class="nk-ibx-stats-text">Skor Rata-rata</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-items-center mb-3">
                            <h6 class="card-title">
                                <em class="icon ni ni-bar-chart me-2"></em> Grafik Ringkasan
                            </h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="summaryChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Skor per Departemen -->
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="card-title-group align-items-center mb-3">
                    <h6 class="card-title">
                        <em class="icon ni ni-table-view me-2"></em> Distribusi Skor per Departemen
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Departemen</th>
                                <th>Skor Rata-rata</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departmentAverages as $departmentName => $average)
                            <?php $department = $departments->firstWhere('nama', $departmentName); ?>
                            <tr>
                                <td>{{ $departmentName }}</td>
                                <td>
                                    <span
                                        class="badge rounded-pill 
                                            {{ $average >= 4 ? 'bg-success' : ($average >= 3 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $average }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('dekan.edom.reports.department', $department->id) }}"
                                        class="btn btn-sm btn-primary">
                                        Lihat Detail
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

    <!-- Tambahan CSS untuk Penyesuaian -->
    @push('style')
    <style>
        .chart-container {
            position: relative;
            width: 100%;
            max-height: 200px;
        }

        .nk-ibx-stats-count {
            font-size: 1.5rem;
            font-weight: 600;
            color: #364a63;
        }

        .nk-ibx-stats-text {
            font-size: 0.875rem;
            color: #8094ae;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
    @endpush

    <!-- Tambahan JavaScript untuk Grafik (Menggunakan Chart.js) -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
                // Inisialisasi Chart.js
                const ctx = document.getElementById('summaryChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jumlah Respondens', 'Skor Rata-rata'],
                        datasets: [{
                            label: 'Ringkasan EDOM',
                            data: [{{ $totalRespondents }}, {{ $facultyAverageRating }}],
                            backgroundColor: ['#3B82F6', '#10B981'],
                            borderColor: ['#2563EB', '#059669'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: Math.max({{ $totalRespondents }}, {{ $facultyAverageRating }}) * 1.2 // Menyesuaikan skala Y
                            }
                        },
                        plugins: {
                            legend: {
                                display: true
                            }
                        }
                    }
                });

                // Inisialisasi Tooltip Bootstrap (jika diperlukan)
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
    </script>
    @endpush
</x-main-layout>