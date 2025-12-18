<x-main-layout>
    @section('title', 'Detail Pengajuan Surat')
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
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('student.request.letter.index') }}"
                                        class="btn btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <div class="nk-block">
                                    <div class="row g-gs">
                                        <!-- Informasi Surat -->
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-inner">
                                                    <h5 class="card-title">Informasi Surat</h5>
                                                    <div class="row g-4">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Jenis Surat</label>
                                                                <div class="form-control-wrap">
                                                                    <p type="text" class="form-control">{{
                                                                        $letterRequest->letterType->name }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Status</label>
                                                                <div class="form-control-wrap">
                                                                    @php
                                                                    $statusClass = [
                                                                    'draft' => 'bg-gray',
                                                                    'submitted' => 'bg-info',
                                                                    'processing' => 'bg-warning',
                                                                    'approved' => 'bg-success',
                                                                    'rejected' => 'bg-danger',
                                                                    'completed' => 'bg-success'
                                                                    ][$letterRequest->status];

                                                                    $statusLabel = [
                                                                    'draft' => 'Draft',
                                                                    'submitted' => 'Menunggu Persetujuan',
                                                                    'processing' => 'Sedang Diproses',
                                                                    'approved' => 'Disetujui',
                                                                    'rejected' => 'Ditolak',
                                                                    'completed' => 'Selesai'
                                                                    ][$letterRequest->status];
                                                                    @endphp
                                                                    <span class="badge {{ $statusClass }}">{{
                                                                        $statusLabel }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($letterRequest->reference_number)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Nomor Surat</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $letterRequest->reference_number }}"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Tanggal Pengajuan</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $letterRequest->created_at->format('d/m/Y H:i') }}"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Data Surat -->
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-inner">
                                                    <h5 class="card-title">Data Surat</h5>
                                                    <div class="row g-4">
                                                        @foreach($letterRequest->form_data as $key => $value)
                                                        @if(in_array($key, $letterRequest->letterType->required_fields))
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">{{ ucwords(str_replace('_', '
                                                                    ', $key)) }}</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $value }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($letterRequest->status === 'rejected')
                                    <div class="nk-block mt-4">
                                        <div class="card bg-danger bg-opacity-10">
                                            <div class="card-inner">
                                                <h5 class="card-title text-danger">Alasan Penolakan</h5>
                                                <p>{{ $letterRequest->rejection_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($letterRequest->document_path)
                                    <div class="nk-block mt-4">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Dokumen Surat</h5>
                                                <a href="{{ route('student.request.letter.download', $letterRequest) }}"
                                                    class="btn btn-primary">
                                                    <em class="icon ni ni-download"></em>
                                                    <span>Download Surat</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($letterRequest->approval_history)
                                    <div class="nk-block mt-4">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Riwayat Persetujuan</h5>
                                                <div class="timeline">
                                                    @foreach($letterRequest->approval_history as $history)
                                                    <div class="timeline-item">
                                                        <div
                                                            class="timeline-status {{ $history['status'] === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                                        </div>
                                                        <div class="timeline-date">{{
                                                            \Carbon\Carbon::parse($history['timestamp'])->format('d/m/Y
                                                            H:i') }}</div>
                                                        <div class="timeline-content">
                                                            <p>{{ $history['reason'] }}</p>
                                                            <span class="smaller text-muted">oleh {{ $history['user']
                                                                }}</span>
                                                        </div>
                                                    </div>
                                                    @endforeach
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