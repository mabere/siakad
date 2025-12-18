<x-main-layout>
    @section('title', 'Verifikasi Pengajuan Ujian Skripsi')

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Daftar Pengajuan Ujian Skripsi Menunggu Verifikasi</h5>
        </div>

        <div class="card-body table-responsive">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Judul Skripsi</th>
                        <th>Jenis Ujian</th>
                        <th>Tgl Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- UBAH VARIABEL LOOPING DARI $theses MENJADI $exams --}}
                    @forelse($exams as $index => $exam)
                    @php
                    $badgeColors = [
                    'diajukan' => 'bg-warning text-dark',
                    'dijadwalkan' => 'bg-info',
                    'diterima' => 'bg-success',
                    'ditolak' => 'bg-danger',
                    'lulus' => 'bg-success',
                    'lulus_revisi' => 'bg-info text-dark',

                    ];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        {{-- UBAH CARA MENGAKSES DATA --}}
                        <td>{{ $exam->thesis->student->user->name }}</td>
                        <td>{{ $exam->thesis->student->nim }}</td>
                        <td>{{ $exam->thesis->title }}</td>
                        <td>{{ ucfirst($exam->exam_type) }}</td>
                        <td>{{ $exam->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $badgeColors[$exam->status] ?? 'bg-secondary' }}">
                                {{ strtoupper(str_replace('_', ' ', $exam->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('ktu.thesis.exam.show', $exam->id) }}" class="btn btn-sm btn-info">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">Belum ada pengajuan ujian skripsi yang menunggu verifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>
