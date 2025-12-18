<x-main-layout>
    @section('title', 'Laporan Statistik Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Laporan Statistik Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Analisis data alumni institusi.</p>
                    </div>
                </div>
                <div class="nk-block-head-actions">
                    <a href="{{ route('alumni.export') }}" class="btn btn-primary">Export Laporan</a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-gs">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-inner">
                            <h6 class="title mb-3">Distribusi Status Kerja</h6>
                            <canvas id="employmentChart" width="400" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-inner">
                            <h6 class="title mb-3">Distribusi Sektor Industri</h6>
                            <canvas id="industryChart" width="400" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-stretch mt-3">
                <div class="card-inner">
                    <h6 class="title mb-3">Statistik Utama</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Total Alumni:</strong> {{ $totalAlumni }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Alumni Bekerja:</strong>
                                {{ $employed }}
                                ({{ $totalAlumni > 0 ? number_format($employed / $totalAlumni * 100, 2) : 0 }}%)
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Melanjutkan Pendidikan:</strong>
                                {{ $furtherEducation }}
                                ({{ $totalAlumni > 0 ? number_format($furtherEducation / $totalAlumni * 100, 2) : 0 }}%)
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Kontribusi:</strong>
                                {{ $contributing }}
                                ({{ $totalAlumni > 0 ? number_format($contributing / $totalAlumni * 100, 2) : 0 }}%)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Employment Chart (Pie Chart)
        const employmentCtx = document.getElementById('employmentChart').getContext('2d');
        new Chart(employmentCtx, {
            type: 'bar',
            data: {
                labels: ['Bekerja', 'Menganggur', 'Pendidikan Lanjutan'],
                datasets: [{
                    data: [
                        {{ $employed }},
                        {{ $totalAlumni - $employed - $furtherEducation }},
                        {{ $furtherEducation }}
                    ],
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 14 }, color: '#1E3A8A' } } }
            }
        });

        // Industry Distribution Chart (Bar Chart)
        const industryCtx = document.getElementById('industryChart').getContext('2d');
        new Chart(industryCtx, {
            type: 'bar',
            data: {
                labels: @json($industryDistribution->pluck('industry')),
                datasets: [{
                    label: 'Jumlah Alumni',
                    data: @json($industryDistribution->pluck('count')),
                    backgroundColor: '#4BC0C0'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { font: { size: 12 }, maxRotation: 45, minRotation: 45 } },
                    y: { beginAtZero: true, ticks: { font: { size: 12 }, color: '#1E3A8A' } }
                }
            }
        });
    </script>
    @endpush
</x-main-layout>
