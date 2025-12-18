<x-main-layout>
    @section('title', 'Statistik Pengumuman')

    <div class="nk-content">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="preview-block">
                    <span class="preview-title-lg overline-title mb-3">Filter Pengumuman</span>
                    <div class="row gy-4">
                        <form method="GET" action="{{ route('admin.statistik.index') }}"
                            class="d-flex ps-3 pe-0 gx-3 justify-around">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="year">Pilih Tahun</label>
                                    <div class="form-control-wrap">
                                        <select name="year" class="form-select js-select2"
                                            onchange="this.form.submit()">
                                            @foreach(range(now()->year, now()->year - 4) as $y)
                                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected'
                                                : ''
                                                }}>Tahun {{ $y }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Pilih Kategori</label>
                                    <div class="form-control-wrap">
                                        <select name="category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Semua Kategori</option>
                                            @foreach($allCategories as $cat)
                                            <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected' : ''
                                                }}>
                                                {{ ucfirst($cat) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Grafik Pengumuman per Bulan --}}
                        <div class="col-md-4">
                            <div class="card card-bordered h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Pengumuman per Bulan</h6>
                                </div>
                                <div class="card-inner">
                                    <canvas id="chartByMonth" height="220"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Grafik Pengumuman per Role Pembuat --}}
                        <div class="col-md-4">
                            <div class="card card-bordered h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Creator Pengumuman</h6>
                                </div>
                                <div class="card-inner">
                                    <canvas id="chartByRole" height="220"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Grafik Pengumuman per Target Role --}}
                        <div class="col-md-4">
                            <div class="card card-bordered h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Sasaran Pengumuman</h6>
                                </div>
                                <div class="card-inner">
                                    <canvas id="chartByTarget" height="220"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Grafik Status Aktif / Nonaktif --}}
                        <div class="col-md-4">
                            <div class="card card-bordered h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Status Pengumuman</h6>
                                </div>
                                <div class="card-inner">
                                    <canvas id="chartByStatus" height="220"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartByMonth = new Chart(document.getElementById('chartByMonth'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(range(1, 12)) !!},
                datasets: [{
                    label: 'Total',
                    data: {!! json_encode($byMonth->values()) !!},
                    backgroundColor: '#4e73df'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const chartByRole = new Chart(document.getElementById('chartByRole'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($byUserRole->keys()) !!},
                datasets: [{
                    data: {!! json_encode($byUserRole->values()) !!},
                    backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            }
        });

        const chartByTarget = new Chart(document.getElementById('chartByTarget'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($byTargetRole->keys()) !!},
                datasets: [{
                    data: {!! json_encode($byTargetRole->values()) !!},
                    backgroundColor: ['#6610f2', '#20c997', '#fd7e14']
                }]
            }
        });

        const chartByStatus = new Chart(document.getElementById('chartByStatus'), {
            type: 'pie',
            data: {
                labels: ['Aktif', 'Nonaktif'],
                datasets: [{
                    data: {!! json_encode([$byStatus[1] ?? 0, $byStatus[0] ?? 0]) !!},
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            }
        });
    </script>
    @endpush
</x-main-layout>
