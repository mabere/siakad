<x-main-layout>
    @section('title', 'Detail Pengajuan Surat')

    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Detail Pengajuan Surat #{{ $letterRequest->id }}</h4>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <em class="icon ni ni-arrow-left me-1"></em> Kembali
            </a>
        </div>

        <!-- Grid Layout -->
        <div class="row g-4">
            <!-- Informasi Pengajuan -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Informasi Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Jenis Surat</dt>
                            <dd class="col-sm-7">{{ $letterRequest->letterType->name }}</dd>

                            <dt class="col-sm-5">Tanggal Pengajuan</dt>
                            <dd class="col-sm-7">
                                {{ $letterRequest->created_at->translatedFormat('l, d F Y') }}
                            </dd>

                            <dt class="col-sm-5">Status Pengajuan</dt>
                            <dd class="col-sm-7">
                                <x-custom.status-badge :status="$letterRequest->status" />
                            </dd>

                            @if($letterRequest->notes)
                            <dt class="col-sm-5">Catatan</dt>
                            <dd class="col-sm-7">
                                <i class="bi bi-info-circle"></i>{{ $letterRequest->notes }}
                            </dd>
                            @endif

                            @if($letterRequest->rejection_reason)
                            <dt class="col-sm-5">Alasan Penolakan</dt>
                            <dd class="col-sm-7 text-danger">
                                <i class="bi bi-x-circle me-2"></i>{{ $letterRequest->rejection_reason }}
                            </dd>
                            @endif

                            @if($letterRequest->status === 'approved' && $letterRequest->document_path)
                            <dt class="col-sm-5">Dokumen Surat</dt>
                            <dd class="col-sm-7">
                                <a href="{{ route('letter.download', $letterRequest) }}" class="btn btn-primary btn-sm">
                                    <i class="icon ni ni-download me-1"></i> Unduh Surat
                                </a>
                            </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Data Formulir Pengajuan -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Data Formulir</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($letterRequest->form_data as $key => $value)
                            <div class="list-group-item px-0">
                                <div class="row">
                                    <div class="col-5 fw-medium">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                    <div class="col-7">{{ $value }}</div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Tidak ada data formulir.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Persetujuan -->
            @if($letterRequest->approval_history)
            <div class="col-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Riwayat Persetujuan</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline ps-3">
                            @foreach($letterRequest->approval_history as $history)
                            <x-custom.timeline-item :step="$history['step']" :timestamp="$history['timestamp']"
                                :user-name="$history['user_name']" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .timeline {
            position: relative;
            border-left: 2px solid #dee2e6;
            margin-left: 1rem;
        }

        .timeline-item {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-status {
            position: absolute;
            left: -10px;
            top: 4px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .list-group-flush .list-group-item {
            background-color: transparent;
        }
    </style>
    @endpush
</x-main-layout>