<x-main-layout>
    @section('title', 'Daftar Skripsi Saya')

    <div class="card mt-3">
        <div class="card-body">
            <h4>Daftar Ujian Tugas Akhir Skripsi</h4>
            <table class="table table-bordered table-hover mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Skripsi</th>
                        <th>Jenis Ujian</th>
                        <th>Status Ujian</th>
                        <th>Status Bimbingan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($theses as $thesis)
                    @php
                    $urut =1;
                    $supervisorsCompleted = $thesis->supervisions->where('status', 'completed')->count();
                    $isSupervisionCompleted = $supervisorsCompleted === 2;
                    @endphp
                    {{-- Loop untuk setiap ujian yang pernah diajukan --}}
                    @forelse($thesis->exams()->orderByDesc('created_at')->get() as $exam)
                    @php
                    $isLatestExam = ($exam->id === $thesis->exams()->latest()->first()->id);
                    $latestExamType = optional($thesis->exams()->latest()->first())->exam_type;

                    // Tentukan teks tombol untuk pendaftaran ujian berikutnya
                    $nextExamText = '';
                    if ($latestExamType === 'proposal') {
                    $nextExamText = 'Daftar Ujian Hasil';
                    } elseif ($latestExamType === 'hasil') {
                    $nextExamText = 'Daftar Ujian Tutup';
                    }
                    @endphp
                    <tr>
                        <td>{{ $urut++ }}.</td>
                        <td>{{ $thesis->title }}</td>
                        <td>{{ ucfirst($exam->exam_type) }}</td>
                        <td>
                            @php
                            $statusColor = 'bg-secondary';
                            $statusText = '';

                            switch($exam->status) {
                            case 'diajukan':
                            $statusText = 'Menunggu Verifikasi';
                            $statusColor = 'bg-info';
                            break;
                            case 'dijadwalkan':
                            $statusText = 'Dijadwalkan';
                            $statusColor = 'bg-primary';
                            break;
                            case 'lulus':
                            $statusText = 'Lulus';
                            $statusColor = 'bg-success';
                            break;
                            case 'lulus_revisi':
                            $statusText = 'Lulus (Revisi)';
                            $statusColor = 'bg-success';
                            break;
                            case 'ditolak':
                            $statusText = 'Ditolak';
                            $statusColor = 'bg-danger';
                            break;
                            default:
                            $statusText = 'Dalam Proses';
                            $statusColor = 'bg-secondary';
                            break;
                            }
                            @endphp
                            <span class="badge {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td>
                            @if ($isSupervisionCompleted)
                            <span class="badge bg-success">Selesai</span>
                            @else
                            <span class="badge bg-warning">Belum Selesai ({{ $supervisorsCompleted }}/2)</span>
                            @endif
                        </td>
                        <td class="d-flex">
                            <a href="{{ route('mahasiswa.thesis.show', $thesis->id) }}" class="btn btn-sm btn-info me-1"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail Skripsi">
                                <em class="icon ni ni-eye"></em>
                            </a>
                            <a href="{{ route('mahasiswa.thesis.exam.show', ['thesis' => $thesis->id, 'exam' => $exam->id]) }}"
                                class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Lihat Detail Ujian">
                                <em class="icon ni ni-search"></em>
                            </a>
                        </td>
                        {{-- Tombol daftar ujian baru, hanya untuk ujian terakhir yang lulus dan bimbingan selesai
                        --}}
                        @if($isLatestExam && $isSupervisionCompleted && in_array($exam->status, ['lulus',
                        'lulus_revisi']))
                        <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                            class="btn btn-sm btn-success me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="{{ $nextExamText }}">
                            <em class="icon ni ni-plus-circle"></em>
                        </a>
                        @endif
                        {{-- Tombol daftar ulang untuk ujian terakhir yang ditolak --}}
                        @if($isLatestExam && $exam->status === 'ditolak')
                        <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                            class="btn btn-sm btn-danger me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Daftar Ulang Ujian {{ ucfirst($exam->exam_type) }}">
                            <em class="icon ni ni-repeat"></em>
                        </a>
                        @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $thesis->title }}</td>
                        <td colspan="2"><span class="badge bg-secondary">Belum Diajukan</span></td>
                        <td>
                            @if ($isSupervisionCompleted)
                            <span class="badge bg-success">Selesai</span>
                            @else
                            <span class="badge bg-warning">Belum Selesai ({{ $supervisorsCompleted }}/2)</span>
                            @endif
                        </td>
                        <td class="d-flex">
                            <a href="{{ route('mahasiswa.thesis.show', $thesis->id) }}" class="btn btn-sm btn-info me-1"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail">
                                <em class="icon ni ni-eye"></em>
                            </a>
                            @if($isSupervisionCompleted)
                            <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                                class="btn btn-sm btn-success me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Daftar Ujian Proposal">
                                <em class="icon ni ni-edit"></em>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Anda belum memiliki pengajuan skripsi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-main-layout>
