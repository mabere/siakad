<x-main-layout>
    @section('title', 'Tambah Monitoring Pembelajaran')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Tambah Monitoring Pembelajaran</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('admin.monitoring.store') }}" method="POST">
                    @csrf

                    {{-- Informasi Jadwal --}}
                    <div class="form-group">
                        <label class="form-label" for="schedule_id">Jadwal Kuliah</label>
                        <select class="form-select @error('schedule_id') is-invalid @enderror" name="schedule_id"
                            id="schedule_id">
                            <option value="">Pilih Jadwal</option>
                            @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}">
                                {{ $schedule->course->name }} -
                                {{ $schedule->lecturersInSchedule->first()->nama_dosen }}
                                ({{ $schedule->hari }}, {{ $schedule->time_start }}-{{ $schedule->time_end }})
                            </option>
                            @endforeach
                        </select>
                        @error('schedule_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Informasi Monitoring --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="meeting_number">Pertemuan Ke</label>
                                <input type="number" class="form-control @error('meeting_number') is-invalid @enderror"
                                    id="meeting_number" name="meeting_number" min="1" max="16"
                                    value="{{ old('meeting_number') }}">
                                @error('meeting_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="monitoring_date">Tanggal Monitoring</label>
                                <input type="date" class="form-control @error('monitoring_date') is-invalid @enderror"
                                    id="monitoring_date" name="monitoring_date" value="{{ old('monitoring_date') }}">
                                @error('monitoring_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="start_time">Waktu Mulai</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="end_time">Waktu Selesai</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time') }}">
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Detail Pembelajaran --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="attendance_count">Jumlah Kehadiran</label>
                                <input type="number"
                                    class="form-control @error('attendance_count') is-invalid @enderror"
                                    id="attendance_count" name="attendance_count" min="0"
                                    value="{{ old('attendance_count') }}">
                                @error('attendance_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kesesuaian Materi</label>
                                <div class="form-control-wrap">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="material_conformity"
                                            id="material_yes" value="1" {{ old('material_conformity')=='1' ? 'checked'
                                            : '' }}>
                                        <label class="custom-control-label" for="material_yes">Sesuai</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="material_conformity"
                                            id="material_no" value="0" {{ old('material_conformity')=='0' ? 'checked'
                                            : '' }}>
                                        <label class="custom-control-label" for="material_no">Tidak Sesuai</label>
                                    </div>
                                </div>
                                @error('material_conformity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="learning_method">Metode Pembelajaran</label>
                                <input type="text" class="form-control @error('learning_method') is-invalid @enderror"
                                    id="learning_method" name="learning_method" value="{{ old('learning_method') }}">
                                @error('learning_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="media_used">Media Pembelajaran</label>
                                <input type="text" class="form-control @error('media_used') is-invalid @enderror"
                                    id="media_used" name="media_used" value="{{ old('media_used') }}">
                                @error('media_used')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                            name="notes">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label">Aspek Penilaian</label>
                        <div class="card card-bordered">
                            <div class="card-inner">
                                @php
                                $aspects = [
                                'rps_conformity' => 'Kesesuaian dengan RPS',
                                'material_delivery' => 'Penyampaian Materi',
                                'student_engagement' => 'Keterlibatan Mahasiswa',
                                'time_management' => 'Manajemen Waktu',
                                'media_effectiveness' => 'Efektivitas Media',
                                'assessment_method' => 'Metode Penilaian'
                                ];
                                @endphp

                                @foreach($aspects as $key => $aspect)
                                <div class="form-group">
                                    <label class="form-label">{{ $aspect }}</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <select name="aspects[{{ $key }}][score]"
                                                class="form-select @error('aspects.'.$key.'.score') is-invalid @enderror">
                                                <option value="">Pilih Skor</option>
                                                <option value="4">4 - Sangat Baik</option>
                                                <option value="3">3 - Baik</option>
                                                <option value="2">2 - Cukup</option>
                                                <option value="1">1 - Kurang</option>
                                            </select>
                                            @error('aspects.'.$key.'.score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="aspects[{{ $key }}][notes]"
                                                class="form-control @error('aspects.'.$key.'.notes') is-invalid @enderror"
                                                placeholder="Catatan untuk {{ strtolower($aspect) }}">
                                            @error('aspects.'.$key.'.notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
