<x-main-layout>
    @section('title', 'Detail Kegiatan Penunjang')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ url('admin/penunjang/validation/index') }}" class="btn btn-outline-secondary">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-lg-8">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Informasi Kegiatan</h6>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <span class="sub-text">Nama Dosen</span>
                                <span class="lead-text">{{ $penunjang->lecturer->nama_dosen }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">NIDN</span>
                                <span class="lead-text">{{ $penunjang->lecturer->nidn }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Program Studi</span>
                                <span class="lead-text">{{ $penunjang->lecturer->department->nama }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Judul Kegiatan</span>
                                <span class="lead-text">{{ $penunjang->title }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Tanggal Kegiatan</span>
                                <span class="lead-text">{{ $penunjang->date->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Level</span>
                                <span class="lead-text">{{ $penunjang->level }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Peran</span>
                                <span class="lead-text">{{ $penunjang->peran }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="sub-text">Penyelenggara</span>
                                <span class="lead-text">{{ $penunjang->organizer }}</span>
                            </div>
                            <div class="col-12">
                                <span class="sub-text">Bukti</span>
                                @if($penunjang->proof)
                                <a href="{{ $penunjang->proof_url }}" class="btn btn-sm btn-primary" target="_blank">
                                    <em class="icon ni ni-link"></em>
                                    <span>Lihat URL</span>
                                </a>

                                @elseif($penunjang->proof_url)
                                <a href="{{ asset('storage/' . $penunjang->proof) }}" class="btn btn-sm btn-primary"
                                    target="_blank">
                                    <em class="icon ni ni-file-pdf"></em>
                                    <span>Lihat File</span>
                                </a>
                                @else
                                <span class="lead-text text-soft">Tidak ada bukti</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-3">
                            <div class="card-title">
                                <h6 class="title">Validasi</h6>
                            </div>
                        </div>

                        @if($penunjang->status == 'pending')
                        <form action="{{ route('admin.penunjang.validation.validate', $penunjang) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" name="status" id="status">
                                        <option value="approved">Setujui</option>
                                        <option value="rejected">Tolak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="rejection-reason-group" style="display: none;">
                                <label class="form-label">Alasan Penolakan</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" name="rejection_reason" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                        @else
                        <div
                            class="alert alert-pro @if($penunjang->status == 'approved') alert-success @else alert-danger @endif">
                            <div class="alert-text">
                                <h6>Status: {{ $penunjang->status == 'approved' ? 'Disetujui' : 'Ditolak' }}</h6>
                                @if($penunjang->rejection_reason)
                                <p>Alasan: {{ $penunjang->rejection_reason }}</p>
                                @endif
                                <small>Divalidasi pada: {{ $penunjang->validated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('status').addEventListener('change', function() {
            const rejectionGroup = document.getElementById('rejection-reason-group');
            rejectionGroup.style.display = this.value === 'rejected' ? 'block' : 'none';
        });
    </script>
    @endpush
</x-main-layout>