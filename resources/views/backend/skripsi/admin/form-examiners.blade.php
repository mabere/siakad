<x-main-layout>
    @section('title', 'Penetapan Penguji Skripsi')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary fw-bold">
                <i class="fas fa-user-graduate me-2"></i>Penetapan Penguji Skripsi
            </h3>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book-open me-2"></i>Detail Skripsi
                </h5>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="text-muted small">Judul Skripsi</span>
                            {{-- MENGGUNAKAN $exam->thesis->title --}}
                            <p class="fw-bold lead">{{ $exam->thesis->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <span class="text-muted small">Nama Mahasiswa</span>
                            {{-- MENGGUNAKAN $exam->thesis->student->user->name --}}
                            <p class="fw-bold">{{ $exam->thesis->student->user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <span class="text-muted small">NIM</span>
                            {{-- MENGGUNAKAN $exam->thesis->student->nim --}}
                            <p class="fw-bold">{{ $exam->thesis->student->nim }}</p>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- MENGGUNAKAN $exam->thesis->title --}}
                <h2>Penguji untuk Ujian Skripsi: {{ $exam->thesis->title }}</h2>
                <div class="mb-3">
                    <label><strong>Dosen Pembimbing:</strong></label>
                    <ul>
                        {{-- MENGGUNAKAN $exam->thesis->supervisions --}}
                        @foreach ($exam->thesis->supervisions as $s)
                        <li>{{ $s->supervisor->nama_dosen }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- MENGGUNAKAN $exam->examiners --}}
                @if($exam->examiners->isNotEmpty())
                <h3>Daftar Penguji</h3>
                <ul>
                    @foreach($exam->examiners as $examiner)
                    <li>
                        {{ $examiner->lecturer->user->name }}
                        (Skor: {{ $examiner->score ?? 'Belum dinilai' }},
                        Komentar: {{ $examiner->comment ?? 'Tidak ada komentar' }})
                    </li>
                    @endforeach
                </ul>
                @else
                <p>Belum ada penguji yang ditugaskan.</p>
                @endif
                {{-- MENGGUNAKAN $exam sebagai parameter route --}}
                <form action="{{ route('kaprodi.thesis.examiners.assign', $exam) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="examiners" class="form-label fw-bold text-primary"><i
                                class="fas fa-users me-1"></i>Pilih Dosen Penguji</label>
                        <small class="text-muted d-block mb-2">Pilih minimal 2 dosen penguji (gunakan Ctrl untuk memilih
                            multiple)</small>

                        <select name="examiners[]" id="examiners" class="form-select select2" multiple="multiple"
                            required style="width: 100%;">
                            @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ in_array($lecturer->id, old('examiners', $examiners))
                                ?
                                'selected' : '' }} @if(in_array($lecturer->id, $supervisors)) disabled
                                @endif>
                                {{ $lecturer->nama_dosen }}
                            </option>
                            @endforeach
                        </select>
                        @error('examiners')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                        @enderror
                    </div>
                    <hr class=" border-top">
                    <div class="py-1 my-3 mb-3">
                        <div class="d-flex justify-content-start gap-2">
                            <button type="reset" class="btn btn-secondary p-3">
                                <i class="icon ni ni-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary p-3">
                                <i class="icon ni ni-save me-1"></i> Simpan Penguji
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih dosen penguji...",
                minimumSelectionLength: 2,
                width: 'resolve'
            });
        });
    </script>
    @endpush
</x-main-layout>
