<x-main-layout>
    @section('title', 'Dashboard Kegiatan Penunjang')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Ringkasan dan statistik kegiatan penunjang Anda</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="row g-gs">
            <!-- Statistik Umum -->
            <div class="col-lg-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Statistik Umum</h6>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $totalKegiatan }}</span>
                                <span class="sub-title">Total Kegiatan</span>
                            </div>
                            <div class="nk-sale-data">
                                <span class="amount">{{ $kegiatanTahunIni }}</span>
                                <span class="sub-title">Kegiatan Tahun {{ date('Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribusi Level -->
            <div class="col-lg-8">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Distribusi Berdasarkan Level</h6>
                            </div>
                        </div>
                        <div class="nk-order-ovwg">
                            <div class="row g-4">
                                <div class="col-sm-4">
                                    <div class="nk-order-ovwg-data buy">
                                        <div class="amount">{{ $levelNasional }} <small>Kegiatan</small></div>
                                        <div class="title"><em class="icon ni ni-flag"></em> Nasional</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="nk-order-ovwg-data sell">
                                        <div class="amount">{{ $levelInternasional }} <small>Kegiatan</small></div>
                                        <div class="title"><em class="icon ni ni-globe"></em> Internasional</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="nk-order-ovwg-data sell">
                                        <div class="amount">{{ $levelRegional }} <small>Kegiatan</small></div>
                                        <div class="title"><em class="icon ni ni-map"></em> Regional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Trend -->
            <div class="col-lg-8">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Tren Kegiatan</h6>
                            </div>
                        </div>
                        <div class="nk-order-ovwg">
                            <canvas id="activityTrend"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kegiatan Terbaru -->
            <div class="col-lg-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner border-bottom">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Kegiatan Terbaru</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-inner">
                        <div class="timeline">
                            @forelse($kegiatanTerbaru as $kegiatan)
                            <div class="timeline-item">
                                <div class="timeline-status bg-primary"></div>
                                <div class="timeline-date">{{ date('d M Y', strtotime($kegiatan->date)) }}</div>
                                <div class="timeline-data">
                                    <h6 class="timeline-title">{{ $kegiatan->title }}</h6>
                                    <div class="timeline-des">
                                        <p>{{ $kegiatan->level }} - {{ $kegiatan->peran }}</p>
                                        <span class="text-muted">{{ $kegiatan->organizer }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-soft">
                                <em class="icon ni ni-info"></em>
                                <p>Belum ada kegiatan</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari controller
        const trendData = @json($chartData);
        
        // Setup grafik trend
        const ctx = document.getElementById('activityTrend').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Jumlah Kegiatan',
                    data: trendData.data,
                    borderColor: '#6576ff',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-main-layout>