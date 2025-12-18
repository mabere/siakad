<x-main-layout>
    <div class="card">
        <div class="card-header">
            <h4>Form Penilaian Ujian Skripsi</h4>
            <p>Mahasiswa: {{ $thesis_exam->thesis->student->user->name }}</p>
            <p>Judul: {{ $thesis_exam->thesis->title }}</p>
        </div>
        <div class="card-body">
            <form action="{{ route('nilai.examiner.exams.storeScore', $thesis_exam->id) }}" method="POST">
                @csrf
                <input type="hidden" name="lecturer_id" value="{{ Auth::user()->lecturer->id }}">

                @forelse ($criterias as $criteria)
                <div class="form-group row mb-3">
                    <label class="col-sm-4 col-form-label">{{ $criteria->name }} (Bobot: {{ $criteria->weight
                        }}%)</label>
                    <div class="col-sm-8">
                        {{-- Ambil nilai yang sudah ada jika dosen sudah pernah mengisi --}}
                        @php
                        $existingScore = optional($scores->where('criteria_id', $criteria->id)->first())->score;
                        $existingNotes = optional($scores->where('criteria_id', $criteria->id)->first())->notes;
                        @endphp
                        <input type="number" name="scores[{{ $criteria->id }}]" class="form-control" value="{{ old("
                            scores.$criteria->id", $existingScore) }}"
                        min="0" max="100" required>
                    </div>
                </div>
                @empty
                <p class="text-danger">Tidak ada kriteria penilaian yang ditemukan untuk jenis ujian ini.</p>
                @endforelse

                {{-- Catatan Tambahan untuk setiap kriteria --}}
                @forelse ($criterias as $criteria)
                <div class="form-group row mb-3">
                    <label class="col-sm-4 col-form-label">Catatan untuk kriteria: "{{ $criteria->name }}"</label>
                    <div class="col-sm-8">
                        @php
                        $existingNotes = optional($scores->where('criteria_id', $criteria->id)->first())->notes;
                        @endphp
                        <textarea name="notes[{{ $criteria->id }}]" class="form-control"
                            rows="2">{{ old("notes.$criteria->id", $existingNotes) }}</textarea>
                    </div>
                </div>
                @empty
                <p class="text-danger">Tidak ada kriteria penilaian yang ditemukan untuk jenis ujian ini.</p>
                @endforelse

                {{-- Catatan umum di tabel thesis_exam_examiners --}}
                <div class="form-group row mb-3">
                    <label class="col-sm-4 col-form-label">Catatan Umum Penguji</label>
                    <div class="col-sm-8">
                        <textarea name="comment" class="form-control"
                            rows="4">{{ optional($thesis_exam->examiners->where('lecturer_id', $lecturer->id)->first())->comment }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>
