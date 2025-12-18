<x-main-layout>
    @section('title', 'Monitoring Pembelajaran')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Monitoring Pembelajaran</h3>
            </div>
            <div class="nk-block-head-content"></div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview mb-3">
            <div class="card-inner">
                <form action="{{ route('admin.monitoring.index') }}" method="GET">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Program Studi</label>
                                <select name="department_id" class="form-select">
                                    <option value="">Semua Prodi</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id')==$department->id ?
                                        'selected' : '' }}>
                                        {{ $department->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Tahun Akademik</label>
                                <select name="academic_year_id" class="form-select">
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id',
                                        getCurrentAcademicYear()->id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->ta }} ({{ $year->semester }})
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

        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Pertemuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monitorings as $monitoring)
                        <tr>
                            <td>{{ $monitoring->monitoring_date }}</td>
                            <td>{{ $monitoring->schedule->start_time . ' - ' . $monitoring->schedule->end_time }}</td>
                            <td>{{ $monitoring->schedule->schedulable->name }}</td>
                            <td>{{ $monitoring->schedule->lecturersInSchedule->first()->nama_dosen }}</td>
                            <td>{{ $monitoring->meeting_number }}</td>
                            <td>
                                <span class="badge bg-{{ $monitoring->status_color }}">{{ $monitoring->status_label
                                    }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.monitoring.show', $monitoring->id) }}"
                                    class="btn btn-sm btn-info">
                                    <em class="icon ni ni-eye"></em>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $monitorings->links() }}
            </div>
        </div>
    </div>
</x-main-layout>
