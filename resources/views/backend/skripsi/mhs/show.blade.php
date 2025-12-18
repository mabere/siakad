<x-main-layout>
    @section('title', 'Detail Skripsi')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Detail Skripsi</h3>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs mb-3">
                            <div class="col">
                                <!-- Informasi Skripsi -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $thesis->title ?? '-' }}</h5>
                                        <p><strong>Status:</strong> {{ ucfirst($thesis->status) }}</p>
                                        <p><strong>Fase Saat Ini:</strong> {{ ucfirst(str_replace('_', ' ',
                                            $thesis->current_phase)) }}</p>
                                        <p><strong>Mulai:</strong> {{ $thesis->start_date?->format('d M Y') }}</p>
                                    </div>
                                </div>

                                <!-- Pembimbing -->
                                <div class="card mb-4">
                                    <div class="card-header"><strong>Pembimbing</strong></div>
                                    <div class="card-body">
                                        @foreach($thesis->supervisions as $supervision)
                                        <p>{{ ucfirst($supervision->supervisor_role) }}: {{
                                            $supervision->supervisor->nama_dosen }}</p>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Log Bimbingan -->
                                <div class="card mb-4">
                                    <div class="card-header"><strong>Log Bimbingan</strong></div>
                                    <div class="card-body">
                                        @forelse($thesis->meetings as $meeting)
                                        <div class="mb-2">
                                            <strong>{{ $meeting->meeting_date->format('d M Y') }}</strong> –
                                            {{ $meeting->topic }} ({{ $meeting->status }})
                                        </div>
                                        @empty
                                        <p>Belum ada log bimbingan.</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Dokumen Pemberkasan -->
                                <div class="card mb-4">
                                    <div class="card-header"><strong>Dokumen Pemberkasan</strong></div>
                                    <div class="card-body">
                                        @forelse($thesis->documents as $doc)
                                        <div class="mb-2">
                                            <strong>{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</strong>:
                                            <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                target="_blank">Lihat</a> –
                                            Status: <span
                                                class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($doc->status) }}
                                            </span>
                                        </div>
                                        @empty
                                        <p>Belum ada dokumen diunggah.</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Form Pengajuan Ujian -->
                                @can('createExam', $thesis)
                                <div class="card mb-4 border-success">
                                    <div class="card-body">
                                        <form action="{{ route('thesis-exam.store', $thesis->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin mengajukan ujian skripsi?')">
                                            @csrf
                                            <button class="btn btn-success">
                                                <i class="fas fa-paper-plane"></i> Ajukan Ujian Skripsi
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-main-layout>