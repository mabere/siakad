<x-main-layout>
    @section('title', 'Detail EDOM Program Studi')

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="display-6 text-primary fw-bold">
                    <i class="icon ni ni-growth me-1"></i>Laporan EDOM - {{ $department->nama }}
                </span>
                <p class="text-muted">Analisis komprehensif evaluasi pembelajaran Program Studi</p>
            </div>
            <a href="{{ route('dekan.edom.index') }}" class="btn btn-light-primary hover-lift">
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
                                <i class="icon ni ni-book text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Total Mata Kuliah</h6>
                                <h2 class="text-white mb-0">{{ $schedules->count() }}</h2>
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
                                <i class="icon ni ni-users text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Total Respons</h6>
                                <h2 class="text-white mb-0">{{ $schedules->sum(fn($s) => $s->responses->count()) }}</h2>
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
                                <i class="icon ni ni-star text-white"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0">Rata-rata Program Studi</h6>
                                <h2 class="text-white mb-0">
                                    @if($averageRatings->isNotEmpty())
                                    {{ number_format($averageRatings->avg(), 2) }}
                                    @else
                                    0.00
                                    @endif
                                </h2>
                                <div class="small text-white mt-1">
                                    @foreach($averageRatings as $category => $avg)
                                    {{ $category }}: {{ number_format($avg, 2) }}<br>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Evaluasi -->
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title text-primary mb-0">
                    <i class="icon ni ni-clipboard me-2"></i>Detail Evaluasi Per Mata Kuliah
                </h5>
                <button class="btn btn-sm btn-soft-primary">
                    <i class="icon ni ni-download me-1"></i>Ekspor Laporan
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">Mata Kuliah</th>
                                <th class="py-3">Dosen Pengampu</th>
                                <th class="py-3 text-center">Respons</th>
                                <th class="py-3">Evaluasi Pembelajaran</th>
                                <th class="py-3 text-center">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            @php
                            $responseCount = $schedule->responses->count();
                            $categoryAverages = $scheduleAverages[$schedule->id] ??
                            $this->calculateAverages(collect([$schedule]));
                            $overallAverage = $categoryAverages->avg() ?? 0;
                            @endphp
                            <tr class="clickable-row" data-bs-toggle="collapse"
                                data-bs-target="#detail-{{ $schedule->id }}">
                                <td class="fw-bold text-primary">{{ $schedule->schedulable->name }}</td>
                                <td>
                                    @foreach($schedule->lecturersInSchedule as $lecturer)
                                    <span class="badge bg-primary rounded-pill me-1">
                                        {{ $lecturer->nama_dosen }}
                                    </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $responseCount > 0 ? 'success' : 'secondary' }} rounded-pill">
                                        {{ $responseCount }} Mahasiswa
                                    </span>
                                </td>
                                <td>
                                    @foreach($categoryAverages as $category => $avg)
                                    <div class="progress-container mb-2">
                                        <div class="d-flex justify-content-between small">
                                            <span>{{ $category }}</span>
                                            <span>{{ number_format($avg, 1) }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ getProgressColor($avg) }}" role="progressbar"
                                                style="width: {{ ($avg / 5) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <span class="display-6 text-{{ getProgressColor($overallAverage) }} fw-bold">
                                        {{ number_format($overallAverage, 1) }}
                                    </span>
                                </td>
                            </tr>
                            <!-- Detail Collapse (Opsional) -->
                            <tr class="collapse" id="detail-{{ $schedule->id }}">
                                <td colspan="5">
                                    <div class="p-3 bg-light">
                                        <p>Detail respons untuk {{ $schedule->course->name }} akan ditampilkan di sini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-light">{{ $schedules->links() }}</div>
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

        .clickable-row {
            cursor: pointer;
            transition: all 0.2s;
        }

        .clickable-row:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }

        .progress-container {
            max-width: 300px;
        }

        .hover-shadow-lg:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</x-main-layout>
