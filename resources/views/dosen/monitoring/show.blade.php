<x-main-layout>
    @section('title', 'Detail Monitoring Pembelajaran')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Detail Monitoring Pembelajaran</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('lecturer.monitoring.index') }}" class="btn btn-outline-primary">
                                    <em class="icon ni ni-arrow-left"></em> Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <dl class="row">
                                    <dt class="col-sm-3">Mata Kuliah</dt>
                                    <dd class="col-sm-9">{{ $monitoring->schedule->course->name }}</dd>

                                    <dt class="col-sm-3">Kelas</dt>
                                    <dd class="col-sm-9">{{ $monitoring->schedule->kelas->name ?? '-' }}</dd>

                                    <dt class="col-sm-3">Pertemuan Ke-</dt>
                                    <dd class="col-sm-9">{{ $monitoring->meeting_number }}</dd>

                                    <dt class="col-sm-3">Tanggal Monitoring</dt>
                                    <dd class="col-sm-9">{{ $monitoring->monitoring_date }}</dd>

                                    <dt class="col-sm-3">Waktu</dt>
                                    <dd class="col-sm-9">{{ $monitoring->start_time }} - {{ $monitoring->end_time }}
                                    </dd>

                                    <dt class="col-sm-3">Jumlah Kehadiran</dt>
                                    <dd class="col-sm-9">{{ $monitoring->attendance_count }}</dd>

                                    <dt class="col-sm-3">Keselarasan Materi</dt>
                                    <dd class="col-sm-9">
                                        @if($monitoring->material_conformity)
                                        <span class="badge bg-success">Sesuai</span>
                                        @else
                                        <span class="badge bg-danger">Tidak Sesuai</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-3">Metode Pembelajaran</dt>
                                    <dd class="col-sm-9">{{ $monitoring->learning_method }}</dd>

                                    <dt class="col-sm-3">Media yang Digunakan</dt>
                                    <dd class="col-sm-9">{{ $monitoring->media_used }}</dd>

                                    <dt class="col-sm-3">Catatan</dt>
                                    <dd class="col-sm-9">{{ $monitoring->notes ?? '-' }}</dd>

                                    <dt class="col-sm-3">Status</dt>
                                    <dd class="col-sm-9">
                                        @if($monitoring->status === 'submitted')
                                        <span class="badge bg-primary">Diajukan</span>
                                        @elseif($monitoring->status === 'verified')
                                        <span class="badge bg-success">Diverifikasi</span>
                                        @elseif($monitoring->status === 'revised')
                                        <span class="badge bg-warning">Perlu Revisi</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-3">Tahun Akademik</dt>
                                    <dd class="col-sm-9">{{ $monitoring->schedule->academicYear->ta ?? '-' }}</dd>

                                    <dt class="col-sm-3">Program Studi</dt>
                                    <dd class="col-sm-9">{{ $monitoring->schedule->department->nama ?? '-' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>