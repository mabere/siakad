<x-main-layout>
    @section('title', 'Detail Nilai Ujian Skripsi')

    <div class="container mt-4">
        <h4>Detail Nilai Ujian - {{ $thesis->student->nama_mhs }} ({{ $thesis->student->nim }})</h4>
        <p><strong>Judul Skripsi:</strong> {{ $thesis->title }}</p>
        <p><strong>Nilai Akhir:</strong> {{ number_format($thesis->exam->final_score, 2) }}</p>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Penguji</th>
                    <th>Skor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($thesis->exam->examiners as $index => $examiner)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $examiner->position . '. ' . $examiner->lecturer->nama_dosen }}</td>
                    <td>{{ $examiner->score ?? '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                            data-bs-target="#revisiModal{{ $examiner->id }}">
                            Revisi Nilai
                        </button>
                    </td>
                </tr>

                <!-- Modal Revisi -->
                <div class="modal fade" id="revisiModal{{ $examiner->id }}" tabindex="-1"
                    aria-labelledby="revisiModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('kaprodi.exam.score.revise', [$thesis->id, $examiner->id]) }}"
                            method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Revisi Nilai Penguji {{ $index + 1 }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="score" class="form-label">Nilai Baru</label>
                                        <input type="number" name="score" class="form-control"
                                            value="{{ $examiner->score }}" required min="0" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reason" class="form-label">Alasan Revisi</label>
                                        <input type="text" name="reason" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan Revisi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</x-main-layout>