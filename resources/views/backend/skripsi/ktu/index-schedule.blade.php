<x-main-layout>
    @section('title', 'Daftar Jadwal Ujian')

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Jadwal Ujian Skripsi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Mahasiswa</th>
                            <th scope="col">Judul Skripsi</th>
                            <th scope="col">Status Ujian</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $exam->thesis->student->nama_mhs }}<br>
                                <span class="text-muted">{{ $exam->thesis->student->nim }}</span>
                            </td>
                            <td>{{ $exam->thesis->title }}</td>
                            <td>
                                @if($exam->status == \App\Models\ThesisExam::STATUS_DISETUJUI_DEKAN)
                                <span class="badge bg-warning text-dark">Siap Dijadwalkan</span>
                                @elseif($exam->status == \App\Models\ThesisExam::STATUS_DIJADWALKAN)
                                <span class="badge bg-success">Sudah Dijadwalkan</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $exam->status))
                                    }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ktu.thesis.schedule.show', ['exam' => $exam->id]) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i> Lihat Jadwal
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data ujian skripsi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $exams->links() }}
            </div>
        </div>
    </div>
</x-main-layout>
