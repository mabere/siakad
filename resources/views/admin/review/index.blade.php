<x-main-layout>
    <div class="card">
        <div class="card-header">
            <h4>Tinjauan Hasil Ujian Tugas Akhir</h4>
            <p>Daftar ujian yang telah selesai dinilai.</p>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($finishedExams->isEmpty())
            <p>Tidak ada ujian yang telah selesai dinilai.</p>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Judul Skripsi</th>
                            <th>Jenis Ujian</th>
                            <th>Nilai Akhir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($finishedExams as $exam)
                        <tr>
                            <td>{{ $exam->thesis->student->user->name }} ({{ $exam->thesis->student->nim }})</td>
                            <td>{{ $exam->thesis->title }}</td>
                            <td>{{ ucfirst($exam->exam_type) }}</td>
                            <td>{{ number_format($exam->final_score, 2) }}</td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($exam->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('review.nilai.ujian.show', $exam->id) }}"
                                    class="btn btn-sm btn-info">Lihat
                                    Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</x-main-layout>
