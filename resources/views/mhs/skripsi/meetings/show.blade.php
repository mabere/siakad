<x-main-layout>
    @section('title', 'Detail Bimbingan Skripsi')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('student.thesis.supervision.index') }}"
                                    class="btn btn-outline-secondary">
                                    <em class="icon ni ni-arrow-left"></em>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-gs">
                                    <!-- Status Badge -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h5 class="card-title mb-0">Status Bimbingan</h5>
                                            </div>
                                            <div>
                                                @if($meeting->status === 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                                @elseif($meeting->status === 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                                @else
                                                <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Meeting Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Pembimbing</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control"
                                                    value="{{ $meeting->supervisor->nama_dosen }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Bimbingan</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control"
                                                    value="{{ $meeting->meeting_date->format('d/m/Y H:i') }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Topik Bimbingan</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" value="{{ $meeting->topic }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Deskripsi</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" rows="4"
                                                    readonly>{{ $meeting->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    @if($meeting->attachment_path)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Lampiran</label>
                                            <div class="form-control-wrap">
                                                <a href="{{ Storage::url($meeting->attachment_path) }}"
                                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <em class="icon ni ni-download"></em>
                                                    <span>Download Lampiran</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Response from Supervisor -->
                                    @if($meeting->status !== 'pending')
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Tanggapan Pembimbing</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1">Tanggal Tanggapan</p>
                                                        <p class="text-dark">{{ $meeting->response_date->format('d/m/Y
                                                            H:i') }}</p>
                                                    </div>
                                                    @if($meeting->notes)
                                                    <div class="col-12">
                                                        <p class="mb-1">Catatan</p>
                                                        <p class="text-dark">{{ $meeting->notes }}</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>