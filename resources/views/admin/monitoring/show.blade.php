<x-main-layout>
    @section('title', 'Detail Monitoring Pembelajaran')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Detail Monitoring Pembelajaran</h3>
                <div class="nk-block-des text-soft">
                    <p>{{ $monitoring->schedule->schedulable->name }} - Pertemuan {{ $monitoring->meeting_number }}</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="btn-group">
                    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline-secondary">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>Kembali</span>
                    </a>
                    @if($monitoring->status === 'submitted')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal">
                        <em class="icon ni ni-check"></em>
                        <span>Verifikasi</span>
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#revisionModal">
                        <em class="icon ni ni-edit"></em>
                        <span>Minta Revisi</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="row g-4">
                    {{-- Informasi Umum --}}
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h5 class="card-title">Informasi Umum</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <span class="sub-text">Mata Kuliah:</span>
                                        <span class="lead-text">{{ $monitoring->schedule->schedulable->name }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Dosen:</span>
                                        <span class="lead-text">{{
                                            $monitoring->schedule->lecturersInSchedule->first()->nama_dosen }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Tanggal Monitoring:</span>
                                        <span class="lead-text">{{ $monitoring->monitoring_date }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Pertemuan Ke:</span>
                                        <span class="lead-text">{{ $monitoring->meeting_number }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Waktu:</span>
                                        <span class="lead-text">{{ $monitoring->schedule->start_time }} - {{
                                            $monitoring->schedule->end_time
                                            }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Status:</span>
                                        <span class="badge bg-{{ $monitoring->status_color }}">
                                            {{ $monitoring->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Pembelajaran --}}
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h5 class="card-title">Detail Pembelajaran</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <span class="sub-text">Jumlah Kehadiran:</span>
                                        <span class="lead-text">{{ $monitoring->attendance_count }} Mahasiswa</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="sub-text">Kesesuaian Materi:</span>
                                        <span class="lead-text">
                                            @if($monitoring->material_conformity)
                                            <span class="text-success">Sesuai</span>
                                            @else
                                            <span class="text-danger">Tidak Sesuai</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-12">
                                        <span class="sub-text">Metode Pembelajaran:</span>
                                        <span class="lead-text">{{ $monitoring->learning_method }}</span>
                                    </div>
                                    <div class="col-12">
                                        <span class="sub-text">Media Pembelajaran:</span>
                                        <span class="lead-text">{{ $monitoring->media_used }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aspek Penilaian --}}
                    <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h5 class="card-title">Aspek Penilaian</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Aspek</th>
                                                <th>Skor</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monitoring->aspects as $aspect)
                                            <tr>
                                                <td>{{ Str::title(str_replace('_', ' ', $aspect->aspect_name)) }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-primary"
                                                            style="width: {{ ($aspect->score/4)*100 }}%">
                                                            {{ $aspect->score }}/4
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $aspect->notes ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    @if($monitoring->notes)
                    <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h5 class="card-title">Catatan Tambahan</h5>
                                <p>{{ $monitoring->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="verifyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Monitoring</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.monitoring.verify', $monitoring->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Catatan Verifikasi (Opsional)</label>
                            <textarea name="verification_notes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="revisionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permintaan Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.monitoring.revision', $monitoring->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Catatan Revisi <span class="text-danger">*</span></label>
                            <textarea name="revision_notes" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Minta Revisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
