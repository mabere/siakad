<x-main-layout>
    @section('title', 'Jadwal Ujian Skripsi')

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title text-white">Jadwal Ujian Skripsi</h5>
            </div>
            <div class="card-inner">
                <div class="row g-4">
                    <!-- Card Informasi Utama -->
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h6 class="title text-uppercase text-muted">Informasi Utama</h6>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Judul Skripsi</label>
                                            <p class="fw-bold">{{ $exam->thesis->title }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Mahasiswa</label>
                                            <p>
                                                {{ $exam->thesis->student->nama_mhs }}<br>
                                                <span class="text-muted">NIM: {{ $exam->thesis->student->nim }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Waktu Ujian</label>
                                            @if($exam->scheduled_at)
                                            <p>
                                                <em class="icon ni ni-calendar me-1"></em>
                                                {{ \Carbon\Carbon::parse($exam->scheduled_at)->translatedFormat('l, d F
                                                Y') }}<br>
                                                <em class="icon ni ni-clock me-1"></em>
                                                {{ \Carbon\Carbon::parse($exam->scheduled_at)->translatedFormat('H:i')
                                                }} WIB
                                            </p>
                                            @else
                                            <span class="badge bg-warning">
                                                <em class="icon ni ni-alert-circle me-1"></em> Belum Dijadwalkan
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Detail Pelaksanaan -->
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h6 class="title text-uppercase text-muted">Detail Pelaksanaan</h6>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Lokasi Ujian</label>
                                            <p>
                                                <em class="icon ni ni-map-pin me-1"></em>
                                                {{ $exam->location ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Ketua Panitia</label>
                                            <p>
                                                <em class="icon ni ni-user-alt me-1"></em>
                                                {{ $exam->chairman?->nama_dosen ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Sekretaris</label>
                                            <p>
                                                <em class="icon ni ni-user me-1"></em>
                                                {{ $exam->secretary?->nama_dosen ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Tim Penguji -->
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h6 class="title text-uppercase text-muted">Pembimbing</h6>
                                <ul class="list-group list-group-flush">
                                    @foreach($exam->thesis->supervisions as $supervision)
                                    <li class="list-group-item">
                                        <div class="user-card">
                                            <div class="user-avatar bg-primary">
                                                <span>{{ strtoupper(substr($supervision->supervisor->user->name, 0, 1))
                                                    }}</span>
                                            </div>
                                            <div class="user-info">
                                                <span class="lead-text">{{ $supervision->supervisor->user->name
                                                    }}</span>
                                                <span class="sub-text">Pembimbing</span>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Card Tim Penguji -->
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-inner">
                                <h6 class="title text-uppercase text-muted">Penguji</h6>
                                <ul class="list-group list-group-flush">
                                    @foreach($exam->examiners as $examiner)
                                    <li class="list-group-item">
                                        <div class="user-card">
                                            <div class="user-avatar bg-info">
                                                <span>{{ strtoupper(substr($examiner->lecturer->nam_dosen, 0, 1))
                                                    }}</span>
                                            </div>
                                            <div class="user-info">
                                                <span class="lead-text">{{ $examiner->lecturer->nama_dosen }}</span>
                                                <span class="sub-text">Penguji</span>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @can('scheduleExam', $exam)
                    <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="d-flex justify-content-center">
                                    @if(!$exam->scheduled_at)
                                    <a href="{{ route('ktu.thesis.schedule.form', $exam->id) }}"
                                        class="btn btn-primary">
                                        <em class="icon ni ni-calendar me-1"></em> Buat Jadwal
                                    </a>
                                    @else
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalForm">
                                        <i class="icon ni ni-edit me-2"></i> Ubah Jadwal
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ubah Jadwal --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFormLabel">Ubah Jadwal Ujian Skripsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ktu.thesis.schedule.update', $exam->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="scheduled_at" class="form-label">Tanggal & Waktu</label>
                                <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                    class="form-control @error('scheduled_at') is-invalid @enderror"
                                    value="{{ old('scheduled_at', optional($exam->scheduled_at)->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label">Ruang Ujian</label>
                                <input type="text" name="location" id="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $exam->location) }}" required>
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="chairman_id" class="form-label">Ketua Panitia</label>
                                <select name="chairman_id" id="chairman_id"
                                    class="form-select @error('chairman_id') is-invalid @enderror" required>
                                    <option disabled>Pilih Ketua</option>
                                    @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" @selected(old('chairman_id', $exam->chairman_id)
                                        == $lecturer->id)>
                                        {{ $lecturer->nama_dosen }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('chairman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="secretary_id" class="form-label">Sekretaris</label>
                                <select name="secretary_id" id="secretary_id"
                                    class="form-select @error('secretary_id') is-invalid @enderror" required>
                                    <option disabled>Pilih Sekretaris</option>
                                    @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" @selected(old('secretary_id', $exam->
                                        secretary_id) == $lecturer->id)>
                                        {{ $lecturer->nama_dosen }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('secretary_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-custom.sweet-alert />
</x-main-layout>
