<x-main-layout>
    @section('title', 'Detail Ujian ' . ucfirst($exam->exam_type))

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Detail Ujian {{ ucfirst($exam->exam_type) }}</h4>
            <div>
                <a href="{{ route('mahasiswa.thesis.show', $thesis->id) }}" class="btn btn-secondary me-2">Kembali ke
                    Detail Skripsi</a>
                @php
                $isSupervisionCompleted = $thesis->supervisions->where('status', 'completed')->count() === 2;
                @endphp
                @if($exam->status === 'ditolak')
                <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}" class="btn btn-warning">Daftar
                    Ulang Ujian</a>
                @elseif(in_array($exam->status, ['lulus', 'lulus_revisi']) && !$isNextExamRegistered)
                @if($exam->exam_type === 'proposal' && $isSupervisionCompleted)
                <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}" class="btn btn-primary">Daftar
                    Ujian Hasil</a>
                @elseif($exam->exam_type === 'hasil' && $isSupervisionCompleted)
                <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}" class="btn btn-primary">Daftar
                    Ujian Tutup</a>
                @endif
                @endif
            </div>
        </div>

        <div class="card-body">
            <h5>Informasi Ujian</h5>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 20%;">Jenis Ujian</th>
                    <td>{{ ucfirst($exam->exam_type) }}</td>
                </tr>
                <tr>
                    <th>Judul Skripsi</th>
                    <td>{{ $thesis->title }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @php
                        $statusMap = [
                        'diajukan' => ['text' => 'Menunggu Verifikasi', 'class' => 'bg-info'],
                        'dijadwalkan' => ['text' => 'Dijadwalkan', 'class' => 'bg-primary'],
                        'lulus' => ['text' => 'Lulus', 'class' => 'bg-success'],
                        'lulus_revisi' => ['text' => 'Lulus (Revisi)', 'class' => 'bg-success'],
                        'ditolak' => ['text' => 'Ditolak', 'class' => 'bg-danger'],
                        ];
                        $status = $statusMap[$exam->status] ?? ['text' => 'Dalam Proses', 'class' => 'bg-secondary'];
                        @endphp
                        <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                    </td>
                </tr>
                @if($exam->scheduled_at)
                <tr>
                    <th>Jadwal Ujian</th>
                    <td>{{ $exam->scheduled_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Lokasi</th>
                    <td>{{ $exam->location ?? '-' }}</td>
                </tr>
                @endif
                @if($exam->examiners->isNotEmpty())
                <tr>
                    <th>Dosen Penguji</th>
                    <td>
                        <ul>
                            @foreach($exam->examiners as $examiner)
                            <li>{{ $examiner->lecturer->nama_dosen }} (Penguji {{ $loop->iteration }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
                @if($exam->final_score)
                <tr>
                    <th>Nilai Akhir</th>
                    <td><span class="badge bg-success fs-5">{{ number_format($exam->final_score, 2) }}</span></td>
                </tr>
                @endif
            </table>

            <h5>Dokumen Pendaftaran</h5>
            @if($exam->documents->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Jenis Dokumen</th>
                        <th>Status Verifikasi</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->documents as $document)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</td>
                        <td>
                            @if($document->status === 'verifikasi')
                            <span class="badge bg-success">Terverifikasi</span>
                            @else
                            <span class="badge bg-warning">Belum Terverifikasi</span>
                            @endif
                        </td>
                        <td>{{ $document->notes ?? '-' }}</td>
                        <td>
                            <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                class="btn btn-sm btn-info">Lihat Dokumen</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-warning">Belum ada dokumen yang diunggah untuk ujian ini.</div>
            @endif
        </div>
    </div>
</x-main-layout>
