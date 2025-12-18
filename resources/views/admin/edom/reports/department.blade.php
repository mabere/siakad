<x-main-layout>
    @section('title', 'Laporan EDOM Program Studi')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">
                                    Laporan EDOM - {{ $department->nama }}
                                </h3>
                                <div class="nk-block-head-content">
                                    <a href="{{ route('admin.edom.reports.department.export', [
                                        'department' => $department->id,
                                        'academic_year_id' => $academicYear->id
                                    ]) }}" class="btn btn-primary">
                                        <em class="icon ni ni-download"></em>
                                        <span>Export PDF</span>
                                    </a>
                                </div>
                                <div class="nk-block-des text-soft">
                                    <p>Tahun Akademik: {{ $academicYear->ta }} - Semester {{ $academicYear->semester }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title mb-4">Total Mata Kuliah</div>
                                        <div class="card-amount">
                                            <span class="amount">{{ $statistics['total_courses'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title mb-4">Total Mahasiswa</div>
                                        <div class="card-amount">
                                            <span class="amount">{{ $statistics['total_students'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title mb-4">Total Dosen</div>
                                        <div class="card-amount">
                                            <span class="amount">{{ $statistics['total_lecturers'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title mb-4">Response Rate</div>
                                        <div class="card-amount">
                                            <span class="amount">{{ number_format($statistics['response_rate'], 2)
                                                }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata per Kategori -->
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h5 class="card-title">Rata-rata Per Kategori</h5>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kategori</th>
                                            <th>Rata-rata</th>
                                            <th>Jumlah MK</th>
                                            <th>Jumlah Mahasiswa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categoryAverages as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $category->category }}</td>
                                            <td>{{ number_format($category->average, 2) }}</td>
                                            <td>{{ $category->course_count }}</td>
                                            <td>{{ $category->student_count }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata per Dosen -->
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h5 class="card-title">Rata-rata Per Dosen</h5>
                                <table class="datatable-init table">
                                    <thead>
                                        <tr>
                                            <th>Nama Dosen</th>
                                            <th>Rata-rata</th>
                                            <th>Jumlah MK</th>
                                            <th>Jumlah Mahasiswa</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lecturerAverages as $lecturer)
                                        <tr>
                                            <td>{{ $lecturer->nama_dosen }}</td>
                                            <td>{{ number_format($lecturer->average, 2) }}</td>
                                            <td>{{ $lecturer->course_count }}</td>
                                            <td>{{ $lecturer->student_count }}</td>
                                            <td>
                                                <a href="{{ route('admin.edom.reports.lecturer.detail', $lecturer->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    Detail
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
            </div>
        </div>
    </div>
</x-main-layout>