<x-main-layout>
    @section('title', 'Laporan EDOM - Semua Program Studi')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Laporan EDOM - Semua Program Studi</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Tahun Akademik: {{ $currentAcademicYear->ta }} - Semester {{
                                        $currentAcademicYear->semester }}
                                    </p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ url('admin/edom/reports/export') }}" class="btn btn-primary">
                                        <em class="icon ni ni-download"></em>
                                        <span>Export Semua</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Program Studi</th>
                                            <th class="text-center">Jumlah MK</th>
                                            <th class="text-center">Jumlah Dosen</th>
                                            <th class="text-center">Jumlah Mahasiswa</th>
                                            <th class="text-center">Rata-rata EDOM</th>
                                            <th class="text-center">Response Rate</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                        <tr>
                                            <td>{{ $department->nama }}</td>
                                            <td class="text-center">{{ $department->course_count }}</td>
                                            <td class="text-center">{{ $department->lecturers_count }}</td>
                                            <td class="text-center">{{ $department->students_count }}</td>
                                            <td class="text-center">
                                                {{ number_format($department->average_rating, 2) }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($department->response_rate, 2) }}%
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a href="{{ route('admin.edom.reports.department', ['department' => $department->id, 'academic_year_id' => $currentAcademicYear->id ]) }}" class="btn btn-sm btn-primary">
                                                        <em class="icon ni ni-eye"></em>
                                                        <span>Detail</span>
                                                    </a>
                                                </div>
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
