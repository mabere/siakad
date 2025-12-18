<x-main-layout>
    @section('title', 'Laporan EDOM')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="btn-group">
                    <a href="{{ url('admin/edom/reports/export-excel', request()->query()) }}" class="btn btn-info"
                        data-toggle="tooltip" data-placement="top" title="Ekspor Excel">
                        <em class="icon ni ni-file-xls"></em>
                    </a>
                    <a href="/admin/edom/reports/export-pdf" class="btn btn-success  ms-1" data-toggle="tooltip"
                        data-placement="top" title="Ekspor Pdf">
                        <em class="icon ni ni-file-pdf"></em>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <!-- Filter -->
        <div class="card card-bordered card-preview mb-3">
            <div class="card-inner">
                <form action="{{ route('admin.edom.reports.index') }}" method="GET">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Tahun Akademik</label>
                                <select class="form-select" name="academic_year_id">
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear==$year->id ? 'selected' : '' }}>
                                        {{ $year->ta }} - {{ $year->semester }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Program Studi</label>
                                <select class="form-select" name="department_id">
                                    <option value="">Semua Program Studi</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $selectedDepartment==$department->id ?
                                        'selected' : '' }}>
                                        {{ $department->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <em class="icon ni ni-filter"></em>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grafik dan Statistik -->
        <div class="row g-gs">
            <!-- Rata-rata per Kategori -->
            <div class="col-md-6">
                <div class="card card-bordered card-preview h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Rata-rata per Kategori</h6>
                            </div>
                        </div>
                        <div class="nk-order-ovwg">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Dosen -->
            <div class="col-md-6">
                <div class="card card-bordered card-preview h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Kinerja Dosen</h6>
                            </div>
                        </div>
                        <div class="nk-order-ovwg">
                            <canvas id="lecturerChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <a href="{{ url('admin/edom/reports/department') }}" class="me-1 btn btn-warning">
                    Buka Halaman Laporan Seluruh Program Studi
                </a>

            </div>
        </div>

        <!-- Tabel Detail -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-inner">
                <table class="datatable-init table">
                    <thead>
                        <tr>
                            <th>Dosen</th>
                            <th>Jumlah MK</th>
                            <th>Jumlah Responden</th>
                            <th>Rata-rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lecturerAverages as $lecturer)
                        <tr>
                            <td>{{ $lecturer->nama_dosen }}</td>
                            <td>{{ $lecturer->course_count }}</td>
                            <td>{{ $lecturer->student_count }}</td>
                            <td>
                                <div class="progress" style="height: 1.4rem;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($lecturer->average/5)*100 }}%">
                                        {{ number_format($lecturer->average, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.edom.reports.lecturer.detail', ['lecturer' => $lecturer->id]) }}"
                                    class="btn btn-sm btn-info">
                                    <em class="icon ni ni-eye"></em>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inisialisasi grafik kategori
        const ctxCategory = document.getElementById('categoryChart');
        new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoryAverages->pluck('category')) !!},
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: {!! json_encode($categoryAverages->pluck('average')) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });

        // Inisialisasi grafik dosen
        const ctxLecturer = document.getElementById('lecturerChart');
        new Chart(ctxLecturer, {
            type: 'bar',
            data: {
                labels: {!! json_encode($lecturerAverages->pluck('nama_dosen')) !!},
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: {!! json_encode($lecturerAverages->pluck('average')) !!},
                    backgroundColor: 'rgba(75, 192, 192,0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 5
                    }
                },
                plugins: {
                    legend: {
                        display: false  // Sembunyikan legend karena hanya 1 dataset
                    }
                },
                maintainAspectRatio: false,  // Tambahkan ini agar chart lebih fleksibel
                height: 400  // Atur tinggi chart
            }
        });
    </script>
    @endpush
</x-main-layout>