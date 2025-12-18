<x-main-layout>
    @section('title', 'Detail Ujian Skripsi')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary fw-bold">
                <i class="fas fa-search me-2"></i>Detail Ujian Skripsi
            </h3>
            <a href="{{ route('dekan.thesis.exam.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book-open me-2"></i>Informasi Skripsi
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1 text-muted small">Judul Skripsi</p>
                        <h4 class="fw-bold">{{ $exam->thesis->title }}</h4>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Status Ujian</p>
                        <span class="badge bg-primary fs-6">{{ strtoupper($exam->status) }}</span><br>
                        <span class="text-mute">{{ $exam->scheduled_at->locale('id')->isoFormat('D MMMM Y HH:mm')
                            }}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Nama Mahasiswa</p>
                        <p class="fw-bold">{{ $exam->thesis->student->user->name }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">NIM</p>
                        <p class="fw-bold">{{ $exam->thesis->student->nim }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Tanggal Pengajuan</p>
                        <p class="fw-bold">{{ $exam->created_at->locale('id')->isoFormat('D MMMM Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-user-tie me-2"></i>Dosen Pembimbing</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach ($exam->thesis->supervisions as $index => $supervision)
                            <li class="fw-bold">{{ $supervision->supervisor->user->name }}</li><small
                                class="text-muted">(Pembimbing {{
                                $index+1 }})</small>

                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Dosen Penguji</h5>
                    </div>
                    <div class="card-body">
                        @forelse($exam->examiners as $examiner)
                        <li class="list-group-item d-flex align-items-center">
                            @if ($examiner->lecturer)
                            <div class="ms-3">
                                <span class="fw-bold">{{ $examiner->lecturer->nama_dosen }}</span>
                                <br>
                                <small class="text-muted">{{ $examiner->lecturer->title }} (Penguji {{ $loop->iteration
                                    }})</small>
                            </div>
                            @endif
                        </li>
                        @empty
                        <li class="list-group-item text-muted">Belum ada dosen penguji yang ditunjuk.</li>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if ($exam->status === 'penguji_ditetapkan' || $exam->status === 'revisi_dekan')
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Aksi Persetujuan</h5>
            </div>
            <div class="card-body d-flex justify-content-start gap-3">
                {{-- Form Persetujuan --}}
                <form action="{{ route('dekan.thesis.exam.approve', $exam) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success p-3">
                        <i class="fas fa-check-circle me-1"></i> Setujui Penguji
                    </button>
                </form>

                {{-- Form Revisi --}}
                <form action="{{ route('dekan.thesis.exam.revisi', $exam) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="revisi_notes" value="Mohon periksa kembali daftar penguji.">
                    <button type="submit" class="btn btn-warning p-3">
                        <i class="fas fa-undo me-1"></i> Minta Revisi
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-main-layout>
