<x-main-layout>
    @section('title', 'Edit Monitoring Pembelajaran')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Edit Monitoring Pembelajaran</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('lecturer.monitoring.index') }}" class="btn btn-outline-secondary">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('lecturer.monitoring.update', $monitoring) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    </div>
                    {{-- Informasi Pertemuan --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="meeting_number">Pertemuan Ke <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('meeting_number') is-invalid @enderror"
                                    name="meeting_number" min="{{ $meetingRange['start'] }}"
                                    max="{{ $meetingRange['end'] }}"
                                    value="{{ old('meeting_number', $monitoring->meeting_number) }}" required>
                                <small class="form-text text-muted">
                                    Rentang pertemuan untuk Anda: {{ $meetingRange['start'] }} - {{ $meetingRange['end']
                                    }}
                                </small>
                                @error('meeting_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="monitoring_date">Tanggal Perkuliahan <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('monitoring_date') is-invalid @enderror"
                                    id="monitoring_date" name="monitoring_date"
                                    value="{{ old('monitoring_date', $monitoring->monitoring_date) }}" required>
                                @error('monitoring_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Waktu Perkuliahan --}}
                    <div class="form-group">
                        <label class="form-label">Waktu Perkuliahan</label>
                        <input type="text" class="form-control"
                            value="{{ $schedule->start_time . ' - ' . $schedule->end_time }}" readonly disabled>
                        <small class="form-text text-muted">Waktu sesuai jadwal perkuliahan</small>
                    </div>

                    @if ($monitoring->revision_notes)
                    <div class="alert alert-warning">
                        <strong>Catatan Revisi:</strong>
                        <p>{{ $monitoring->revision_notes }}</p>
                    </div>
                    @endif

                    {{-- Detail Pembelajaran --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="attendance_count">Jumlah Kehadiran Mahasiswa <span
                                        class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('attendance_count') is-invalid @enderror"
                                    id="attendance_count" name="attendance_count" min="0"
                                    value="{{ old('attendance_count', $monitoring->attendance_count) }}" required>
                                @error('attendance_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kesesuaian dengan RPS <span
                                        class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="material_conformity"
                                            id="material_yes" value="1" {{ old('material_conformity',
                                            $monitoring->material_conformity) == '1' ? 'checked' : '' }} required>
                                        <label class="custom-control-label" for="material_yes">Sesuai</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="material_conformity"
                                            id="material_no" value="0" {{ old('material_conformity',
                                            $monitoring->material_conformity) == '0' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="material_no">Tidak Sesuai</label>
                                    </div>
                                </div>
                                @error('material_conformity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Metode dan Media --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="learning_method">Metode Pembelajaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('learning_method') is-invalid @enderror"
                                    id="learning_method" name="learning_method"
                                    value="{{ old('learning_method', $monitoring->learning_method) }}"
                                    placeholder="Contoh: Ceramah, Diskusi, Praktikum" required>
                                @error('learning_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="media_used">Media Pembelajaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('media_used') is-invalid @enderror"
                                    id="media_used" name="media_used"
                                    value="{{ old('media_used', $monitoring->media_used) }}"
                                    placeholder="Contoh: PPT, Video, Whiteboard" required>
                                @error('media_used')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan Pembelajaran</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                            rows="3"
                            placeholder="Catatan tentang jalannya pembelajaran, kendala, atau hal penting lainnya">{{ old('notes', $monitoring->notes) }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('lecturer.monitoring.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-main-layout>
