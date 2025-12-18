<x-main-layout>
    @section('title', 'Detail Ujian Skripsi')

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    {{-- {{ dd($exam) }} --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Detail Ujian Skripsi</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Informasi Mahasiswa</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama:</strong> {{ $exam->thesis->student->user->name }}</p>
                        <p><strong>NIM:</strong> {{ $exam->thesis->student->nim }}</p>
                        <p><strong>Program Studi:</strong> {{ $exam->thesis->student->department->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Judul Skripsi:</strong> {{ $exam->thesis->title }}</p>
                        <p><strong>Dosen Pembimbing:</strong>
                            @forelse($exam->thesis->supervisions as $supervision)
                            <li class="list-group-item">
                                <strong>{{ ucwords(str_replace('_', ' ', $supervision->supervisor_role)) }}</strong>:
                                {{ $supervision->supervisor->user->name ?? '-' }}
                            </li>
                            @empty
                            <li class="list-group-item">
                                <span class="text-muted">Pembimbing belum ditetapkan.</span>
                            </li>
                            @endforelse
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Status & Catatan Ujian</h6>
                <p><strong>Status Terkini:</strong>
                    @php
                    $badgeClass = match($exam->status) {
                    'terverifikasi' => 'bg-primary',
                    'penguji_ditetapkan' => 'bg-info text-dark',
                    'revisi_dekan' => 'bg-warning',
                    'disetujui_dekan' => 'bg-success',
                    'dijadwalkan' => 'bg-info',
                    'pelaksanaan' => 'bg-info',
                    'lulus', 'lulus_revisi', 'selesai' => 'bg-success',
                    'ditolak' => 'bg-danger',
                    default => 'bg-secondary'
                    };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $exam->status)) }}</span>
                </p>
                @if ($exam->status === 'terverifikasi')
                <a href="{{ route('kaprodi.thesis.examiners.form', $exam->id) }}" class="btn btn-md btn-primary">
                    <em class="icon ni ni-users me-1"></em> Tetapkan Penguji
                </a>
                @elseif ($exam->status === 'penguji_ditetapkan' || $exam->status === 'revisi_dekan')
                <a href="{{ route('kaprodi.thesis.examiners.form', $exam->id) }}" class="btn btn-md btn-warning">
                    <em class="icon ni ni-edit me-1"></em> Ubah Penguji
                </a>
                @endif
                @if($exam->revisi_notes)
                <div class="alert alert-warning mt-3">
                    <p class="mb-0"><strong>Catatan Ujian:</strong> {{ $exam->revisi_notes }}</p>
                </div>
                @endif
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Penguji Ujian</h6>
                @if($exam->examiners->count() > 0)
                <ul class="list-group">
                    @foreach ($exam->examiners as $examiner)
                    <li class="list-group-item">
                        {{ $examiner->lecturer->user->nama_dosen }}
                        <span class="badge bg-secondary ms-2">{{ ucwords($examiner->pivot->role) }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted">Penguji belum ditetapkan.</p>
                @endif
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Jadwal Ujian</h6>
                @if($exam->examSchedule)
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($exam->examSchedule->date)->translatedFormat('d F
                    Y') }}</p>
                <p><strong>Waktu:</strong> {{ $exam->examSchedule->time }} WITA</p>
                <p><strong>Ruangan:</strong> {{ $exam->examSchedule->room }}</p>
                @else
                <p class="text-muted">Ujian belum dijadwalkan.</p>
                @endif
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Dokumen Ujian</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>Status Verifikasi</th>
                                <th>Catatan KTU</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exam->documents as $document)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $document->document_type)) }}</td>
                                <td>
                                    @php
                                    $docBadgeColor = match($document->status) {
                                    'verifikasi' => 'bg-success',
                                    'revisi' => 'bg-danger',
                                    default => 'bg-warning'
                                    };
                                    @endphp
                                    <span class="badge {{ $docBadgeColor }}">
                                        {{ ucwords($document->status) }}
                                    </span>
                                </td>
                                <td>{{ $document->notes ?? '-' }}</td>
                                <td>
                                    @if($document->file_path)
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">Lihat</a>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada dokumen yang diunggah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
