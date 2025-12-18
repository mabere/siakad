<x-main-layout>
    @section('title', 'Laporan Edom Program Studi')

    <div class="container-fluid px-4">
        <!-- Header dengan Gradient -->
        <div class="header-title bg-gradient-primary-to-secondary p-4 mb-4 rounded-3 shadow">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-white mb-1">Laporan Evaluasi Dosen dan Mata Kuliah</h4>
                    <p class="text-white-50 mb-0">Program Studi {{ $department->nama }}</p>
                </div>
                <a href="{{ route('kaprodi.edom.index') }}" class="btn btn-outline-light">
                    <i class="icon ni ni-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Ringkasan Departemen -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="icon ni ni-pie-fill me-2"></i>Ringkasan EDOM Departemen</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="border p-3 rounded-3 h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="icon ni ni-book text-primary fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted">Mata Kuliah Dievaluasi</p>
                                    <h3 class="mb-0">{{ $schedules->total() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border p-3 rounded-3 h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                    <i class="icon ni ni-users text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted">Total Respons Mahasiswa</p>
                                    <h3 class="mb-0">{{ $schedules->sum(fn($schedule) => $schedule->responses->count())
                                        }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border p-3 rounded-3 h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                    <i class="icon ni ni-star text-info fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted">Skor Rata-rata</p>
                                    @foreach ($averageRatings as $category => $avg)
                                    <p class="mb-1">{{ $category }}: <strong>{{ number_format($avg, 2) }}</strong></p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Laporan Evaluasi -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="icon ni ni-list me-2"></i>Detail Evaluasi Mata Kuliah
                    </h5>
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control form-control-sm me-2"
                            placeholder="Cari...">
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="icon ni ni-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-primary">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Mata Kuliah</th>
                                <th width="25%">Dosen Pengampu</th>
                                <th width="10%">Status</th>
                                <th width="10%">Respons</th>
                                <th width="15%">Skor Rata-rata</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ ($schedules->currentPage() - 1) * $schedules->perPage() + $loop->iteration }}.
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $schedule->schedulable->name }}</div>
                                    <small class="text-muted">{{ $schedule->schedulable->kode }}</small>
                                </td>
                                <td>
                                    @foreach ($schedule->lecturersInSchedule as $lecturer)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="user-avatar bg-primary rounded-circle me-1"
                                                style="height: 30px;width: 30px;">
                                                {{ substr($lecturer->nama_dosen, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>{{ $lecturer->nama_dosen }}</div>
                                    </div>
                                    @endforeach
                                </td>
                                <td>
                                    <span
                                        class="badge rounded-pill {{ $schedule->responses->count() > 0 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $schedule->responses->count() > 0 ? 'Sudah Dievaluasi' : 'Belum Dievaluasi'
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="icon ni ni-user-check-fill text-muted me-2"></i>
                                        {{ $schedule->responses->count() }}
                                    </div>
                                </td>
                                <td>
                                    @if ($schedule->responses->count() > 0)
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @php
                                            $averages = is_array($scheduleAverages[$schedule->id])
                                            ? $scheduleAverages[$schedule->id]
                                            : $scheduleAverages[$schedule->id]->toArray();
                                            $overallAvg = array_sum($averages) / count($averages);
                                            @endphp
                                            <div class="progress" style="height: 6px; width: 50px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: {{ ($overallAvg/5)*100 }}%"
                                                    aria-valuenow="{{ $overallAvg }}" aria-valuemin="0"
                                                    aria-valuemax="5"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="fw-semibold">{{ number_format($overallAvg, 1) }}/5.0</small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kaprodi.edom.reports.schedule', $schedule->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="icon ni ni-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($schedules->isEmpty())
                <div class="text-center py-5">
                    <img src="{{ asset('assets/img/empty.svg') }}" alt="Empty" style="height: 150px;" class="mb-3">
                    <h5 class="text-muted">Data evaluasi tidak ditemukan</h5>
                </div>
                @endif

                <!-- Pagination Links -->
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted">
                        Menampilkan {{ $schedules->firstItem() }} - {{ $schedules->lastItem() }} dari {{
                        $schedules->total() }} hasil
                    </div>
                    <div>
                        {{ $schedules->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS -->
    <style>
        .bg-gradient-primary-to-secondary {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
        }

        .card {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead th {
            border-bottom: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .avatar-xs {
            width: 24px;
            height: 24px;
            font-size: 0.75rem;
        }

        .progress {
            background-color: #e9ecef;
        }
    </style>

    <!-- JavaScript untuk pencarian -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    </script>
</x-main-layout>