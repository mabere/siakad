<x-custom.sweet-alert />

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    #genderChart,
    #barChart,
    #courseChart {
        padding: 1rem;
    }

    .chart-legend {
        font-size: 14px;
        color: #1E3A8A;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%;
        }

        .card {
            margin-bottom: 1rem;
        }

        canvas {
            width: 100% !important;
            height: 250px !important;
        }
    }
</style>

<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Dashboard</h3>
                <div class="nk-block-des text-soft">
                    <p>Selamat datang di Dashboard Admin</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Box Statistik Tambahan --}}
    <div class="nk-block">
        <div class="row g-gs mb-3">
            @php
            $cards = [
            ['title' => 'Total Fakultas', 'count' => $data['facultyCount'], 'link' => '/admin/faculty'],
            ['title' => 'Total Program Studi', 'count' => $data['departmentCount'], 'link' => '/admin/prodi'],
            ['title' => 'Alumni', 'count' => $data['alumniCount'], 'link' => '/admin/alumni'],
            ['title' => 'Total Mata Kuliah', 'count' => $data['courseCount'], 'link' => '/admin/mk']
            ];
            @endphp

            @foreach ($cards as $card)
            <div class="col-xxl-3 col-md-3">
                <div class="card shadow-lg rounded-lg overflow-hidden">
                    <div class="card-header text-white font-bold text-lg py-3 px-4 bg-warning">
                        <h6>{{ $card['title'] }}</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="text-3xl font-bold text-gray-100 mb-3">{{ $card['count'] }}</div>
                        <a href="{{ $card['link'] }}"
                            class="mt-3 inline-block bg-blue text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition">
                            Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mb-3 row g-gs">
            {{-- Chart Alumni --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Distribusi Status Alumni" id="alumniChart" />
            </div>

            {{-- Gender Pie Chart --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Mahasiswa Berdasarkan Gender" id="genderChart" />
            </div>

            {{-- Status Mahasiswa --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Distribusi Status Mahasiswa" id="statusChart" />
            </div>

            {{-- Mahasiswa per Prodi --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Mahasiswa Berdasarkan Program Studi" id="departmentChart" />
            </div>

            {{-- Mahasiswa per Fakultas --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Mahasiswa Berdasarkan Fakultas" id="barChart" />
            </div>

            {{-- Mata Kuliah per Fakultas --}}
            <div class="col-md-6 col-12">
                <x-chart.canvas title="Mata Kuliah Berdasarkan Fakultas" id="courseChart" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>

<script>
    // Alumni Pie Chart
    const alumniCtx = document.getElementById('alumniChart').getContext('2d');
    new Chart(alumniCtx, {
        type: 'pie',
        data: @json($alumniChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 14 }, color: '#1E3A8A' }
                }
            }
        }
    });

    // Gender Pie Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'pie',
        data: @json($genderChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 14 }, color: '#1E3A8A' }
                }
            }
        }
    });

    // Status Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: @json($statusChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 14 }, color: '#1E3A8A' }
                }
            }
        }
    });

    // Bar Chart Mahasiswa per Department
    const departmentChartCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentChart = new Chart(departmentChartCtx, {
        type: 'bar',
        data: @json($departmentChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Mahasiswa'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Program Studi'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: false
                }
            }
        }
    });

    // Mahasiswa Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: @json($barChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Fakultas',
                        font: { size: 14 }
                    },
                    ticks: {
                        font: { size: 14 },
                        maxRotation: 0,
                        minRotation: 0,
                    }
                },

                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Mahasiswa',
                        font: { size: 14 }
                    },
                    ticks: { font: { size: 12 }, color: '#1E3A8A' }
                }
            }
        }
    });

    // Mata Kuliah Bar Chart
    const courseCtx = document.getElementById('courseChart').getContext('2d');
    new Chart(courseCtx, {
        type: 'bar',
        data: @json($courseChartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Fakultas',
                        font: { size: 14 }
                    },
                    ticks: {
                        font: { size: 14 },
                        maxRotation: 0,
                        minRotation: 0,
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Mahasiswa',
                        font: { size: 14 }
                    },
                    ticks: { font: { size: 12 }, color: '#1E3A8A' }
                }
            }
        }
    });

</script>
@endpush
