<x-main-layout>
    @section('title', 'Input Nilai Ujian Skripsi')

    <div class="card">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="card-body">
            <h4>Input Nilai</h4>
            <p><strong>Nama Mahasiswa:</strong> {{ $thesis->student->nama_mhs }}</p>
            <p><strong>Judul:</strong> {{ $thesis->title }}</p>

            <form action="{{ route('lecturer.exam.score.store', $thesis->id) }}" method="POST">
                @csrf
                <div class="form-group mt-2">
                    <label for="score">Nilai Anda (0 - 100)</label>
                    <input type="number" name="score" class="form-control" min="0" max="100"
                        value="{{ old('score', $examiner->score) }}" required>
                </div>
                <div class="form-group mt-3">
                    <button class="btn btn-primary">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>