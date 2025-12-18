<x-main-layout>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Evaluasi Dosen Mengajar</h3>
                <div class="nk-block-des text-soft">
                    <p>Daftar mata kuliah yang perlu dievaluasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        @if (session('success'))
        <div class="alert alert-success alert-icon">
            <em class="icon ni ni-check-circle"></em>
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-icon">
            <em class="icon ni ni-cross-circle"></em>
            {{ session('error') }}
        </div>
        @endif

        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="tb-lead">{{ $schedule->course->name }}</span>
                                <span class="tb-sub">{{ $schedule->course->code }}</span>
                            </td>
                            <td>
                                @foreach($schedule->lecturersInSchedule as $lecturer)
                                <span class="tb-sub d-block">{{ $lecturer->nama_dosen }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($schedule->hasFilledEdom)
                                <span class="badge bg-success">Sudah Diisi</span>
                                @else
                                <span class="badge bg-warning">Belum Diisi</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$schedule->hasFilledEdom)
                                <a href="{{ route('student.edom.create', $schedule->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Isi EDOM</span>
                                </a>
                                @else
                                <span class="badge bg-success">Sudah Diisi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-main-layout>