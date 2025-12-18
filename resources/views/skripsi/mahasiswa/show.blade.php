<x-main-layout>
    @section('title', 'Detail Skripsi')
    <x-custom.sweet-alert />

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-header border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Informasi Perndaftaran Ujian</h4>
                                    <a href="{{ route('mahasiswa.thesis.index') }}"
                                        class="btn btn-dim btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left"></em> Kembali
                                    </a>
                                </div>
                            </div>
                            <div class="card-inner">
                                @php
                                $latestExam = $thesis->exams()->latest()->first();
                                $isSupervisionCompleted = $thesis->supervisions->where('status', 'completed')->count()
                                === 2;
                                @endphp
                                <div class="row">
                                    <!-- Student Information Card -->
                                    <div class="col-6">
                                        <div class="card card-bordered mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title">Informasi Mahasiswa</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Nama Mahasiswa</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{
                                                                    $thesis->student->nama_mhs
                                                                    }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">NIM</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{
                                                                    $thesis->student->nim }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label">Judul Skripsi</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{ $thesis->title }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Supervision Information Card -->
                                    <div class="col-6">
                                        <div class="card card-bordered mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title">Informasi Pembimbing</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Peran</th>
                                                                <th>Nama Dosen</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($thesis->supervisions as $supervision)
                                                            <tr>
                                                                <td>{{ ucfirst(str_replace('_', ' ',
                                                                    $supervision->supervisor_role)) }}</td>
                                                                <td>{{ $supervision->supervisor->nama_dosen }}</td>
                                                                <td>
                                                                    @if($supervision->status === 'completed')
                                                                    <span
                                                                        class="badge badge-dim bg-outline-success">Selesai</span>
                                                                    @else
                                                                    <span
                                                                        class="badge badge-dim bg-outline-warning">Aktif</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Exam Status Card -->
                                    <div class="col-12">
                                        <div class="card card-bordered">
                                            <div class="card-header">
                                                <h5 class="card-title">Status Ujian</h5>
                                            </div>
                                            <div class="card-body">
                                                @if(!$latestExam)
                                                <div class="alert alert-info">
                                                    <div class="alert-icon">
                                                        <em class="icon ni ni-info"></em>
                                                    </div>
                                                    <div class="alert-text">
                                                        Belum ada pengajuan ujian skripsi.
                                                    </div>
                                                </div>
                                                @else
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Jenis Ujian</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{
                                                                    $latestExam->exam_type
                                                                    ?
                                                                    ucfirst($latestExam->exam_type) : '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($latestExam->scheduled_at)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Jadwal Ujian</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{
                                                                    \Carbon\Carbon::parse($latestExam->scheduled_at)->format('d
                                                                    M Y H:i') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Tempat Ujian</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext">{{
                                                                    $latestExam->location
                                                                    ??
                                                                    '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($latestExam->final_score)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Nilai Akhir</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext"><span
                                                                        class="badge bg-success">{{
                                                                        number_format($latestExam->final_score, 2)
                                                                        }}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Status Kelulusan</label>
                                                            <div class="form-control-wrap">
                                                                <p class="form-control-plaintext"><span
                                                                        class="badge bg-success">{{
                                                                        ucfirst($latestExam->status)
                                                                        }}</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col-6">
                                                        @if($latestExam->examiners->isNotEmpty())
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Dosen Penguji</label>
                                                                <div class="form-control-wrap">
                                                                    <ul class="list-group">
                                                                        @foreach($latestExam->examiners as $examiner)
                                                                        <li class="list-group-item">Penguji
                                                                            {{
                                                                            $loop->iteration }}: {{
                                                                            $examiner->lecturer->nama_dosen }} </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($latestExam->status === 'lulus_revisi' || $latestExam->status
                                                ===
                                                'ditolak')
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Catatan Revisi</label>
                                                        <div class="form-control-wrap">
                                                            <div class="card card-bordered bg-light">
                                                                <div class="card-body">
                                                                    @foreach($latestExam->documents as $document)
                                                                    @if($document->notes)
                                                                    <p><strong>{{ ucfirst(str_replace('_', ' ',
                                                                            $document->document_type)) }}:</strong>
                                                                        {{
                                                                        $document->notes }}</p>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($latestExam->notes)
                                                                    <p><strong>Catatan Ujian:</strong> {{
                                                                        $latestExam->notes
                                                                        }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            @if (!$latestExam)
                                            @if ($isSupervisionCompleted)
                                            <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em> Daftar Ujian Proposal
                                            </a>
                                            @else
                                            <span class="badge badge-dim bg-outline-warning">Menunggu Bimbingan
                                                Selesai</span>
                                            @endif
                                            @else
                                            @switch($latestExam->status)
                                            @case('diajukan')
                                            @case('dijadwalkan')
                                            @case('pelaksanaan')
                                            <span class="badge badge-lg bg-info">Tahapan Ujian {{
                                                $latestExam->exam_type }}
                                                sedang diproses</span>
                                            @break

                                            @case('ditolak')
                                            <span class="badge badge-dim bg-outline-danger">Ujian {{
                                                $latestExam->exam_type
                                                }} Ditolak</span>
                                            <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                                                class="btn btn-warning ms-2">
                                                <em class="icon ni ni-edit"></em> Daftar Ulang Ujian {{
                                                ucfirst($latestExam->exam_type) }}
                                            </a>
                                            @break

                                            @case('lulus')
                                            @case('lulus_revisi')
                                            @php
                                            $badgeClass = $latestExam->status === 'lulus' ? 'bg-outline-success' :
                                            'bg-outline-info';
                                            $badgeText = $latestExam->status === 'lulus' ? 'Lulus' : 'Lulus (Revisi)';
                                            $nextExamText = ($latestExam->exam_type === 'proposal') ? 'Daftar Ujian
                                            Hasil' :
                                            'Daftar Ujian Tutup';
                                            @endphp
                                            @if ($latestExam->exam_type !== 'tutup')
                                            <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em> {{ $nextExamText }}
                                            </a>
                                            @else
                                            <span class="badge badge-dim bg-outline-secondary">Semua Ujian
                                                Selesai</span>
                                            @endif
                                            @break
                                            @endswitch
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
