<x-main-layout>
    @section('title', 'Penjadwalan Ujian Skripsi')

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Penjadwalan Ujian</h5>
            <a href="{{ route('ktu.thesis.schedule.index') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <h4 class="fw-bold mb-3">Penjadwalan Ujian untuk Skripsi: {{ $exam->thesis->title }}</h4>
            <p>Nama Mahasiswa: <strong>{{ $exam->thesis->student->nama_mhs }}</strong></p>

            {{-- Form Penjadwalan --}}
            <form action="{{ route('ktu.thesis.schedule.store', $exam->id) }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="chairman_id" class="form-label">Ketua Panitia</label>
                        <select name="chairman_id" id="chairman_id"
                            class="form-select @error('chairman_id') is-invalid @enderror" required>
                            <option value="">Pilih Ketua</option>
                            @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected(old('chairman_id', $exam->chairman_id) ==
                                $lecturer->id)>
                                {{ $lecturer->nama_dosen }}
                            </option>
                            @endforeach
                        </select>
                        @error('chairman_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="secretary_id" class="form-label">Sekretaris Panitia</label>
                        <select name="secretary_id" id="secretary_id"
                            class="form-select @error('secretary_id') is-invalid @enderror" required>
                            <option value="">Pilih Sekretaris</option>
                            @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected(old('secretary_id', $exam->secretary_id) ==
                                $lecturer->id)>
                                {{ $lecturer->nama_dosen }}
                            </option>
                            @endforeach
                        </select>
                        @error('secretary_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="scheduled_at" class="form-label">Tanggal & Waktu Ujian</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                            class="form-control @error('scheduled_at') is-invalid @enderror"
                            value="{{ old('scheduled_at', optional($exam->scheduled_at)->format('Y-m-d\TH:i')) }}"
                            required>
                        @error('scheduled_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label">Lokasi Ujian</label>
                        <input type="text" name="location" id="location"
                            class="form-control @error('location') is-invalid @enderror"
                            value="{{ old('location', $exam->location) }}" required>
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>
