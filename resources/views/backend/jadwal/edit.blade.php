<x-main-layout>
    @section('title', 'Edit Jadwal Kuliah')
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">@yield('title')</span>
                <form action="{{ route('admin.update-jadwal.dosen', $schedule->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="department_id" value="{{ $schedule->department_id }}">
                    <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">

                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="course_id">Mata Kuliah</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" id="course_id" name="course_id" @error('course_id')
                                        border-red-300 @enderror>
                                        <option value="">Pilih Mata Kuliah</option>
                                        @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" {{ $schedule->course_id == $course->id ?
                                            'selected' : '' }}>
                                            {{ $course->smt }} | {{ $course->name }} | {{ $course->sks }} SKS
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('course_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="kelas_id" @error('kelas_id') border-red-300
                                        @enderror>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelas as $kls)
                                        <option value="{{ $kls->id }}" {{ $schedule->kelas_id == $kls->id ? 'selected' :
                                            '' }}>
                                            {{ $kls->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('kelas_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Dosen 1</label>
                                <div class="form-control-wrap mb-2">
                                    <select class="form-control" name="lecturer1_id" @error('lecturer1_id')
                                        border-red-300 @enderror>
                                        <option value="">Pilih Dosen 1</option>
                                        @foreach ($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ $schedule->lecturer1_id == $lecturer->id
                                            ? 'selected' : '' }}>
                                            {{ $lecturer->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="lecturer1_start">Minggu Awal</label>
                                        <input type="number" min="1" max="16" name="lecturer1_start"
                                            class="form-control" placeholder="Minggu Awal"
                                            value="{{ old('lecturer1_start', $schedule->lecturer1_start) }}">
                                        @error('lecturer1_start') <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="lecturer1_end">Minggu Akhir</label>
                                        <input type="number" min="1" max="16" name="lecturer1_end" class="form-control"
                                            placeholder="Minggu Akhir"
                                            value="{{ old('lecturer1_end', $schedule->lecturer1_end) }}">
                                        @error('lecturer1_end') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Dosen 2 (Opsional)</label>
                                <div class="form-control-wrap mb-2">
                                    <select class="form-control @error('lecturer2_id')
                                        border-red-300 @enderror" name="lecturer2_id">
                                        <option value="">Pilih Dosen 2</option>
                                        @foreach ($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ $schedule->lecturer2_id == $lecturer->id
                                            ? 'selected' : '' }}>
                                            {{ $lecturer->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="lecturer2_start">Minggu Awal</label>
                                        <input type="number" min="1" max="16" name="lecturer2_start"
                                            class="form-control @error('lecturer2_start') border-red-300 @enderror"
                                            placeholder="Minggu Awal"
                                            value="{{ old('lecturer2_start', $schedule->lecturer2_start) }}">
                                        @error('lecturer2_start') <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="lecturer2_end">Minggu Akhir</label>
                                        <input type="number" min="1" max="16" name="lecturer2_end"
                                            class="form-control @error('lecturer2_end') border-red-300 @enderror"
                                            placeholder="Minggu Akhir"
                                            value="{{ old('lecturer2_end', $schedule->lecturer2_end) }}">
                                        @error('lecturer2_end') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="room_id">Ruangan</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="room_id" @error('room_id') border-red-300
                                        @enderror>
                                        <option value="">Pilih Ruangan</option>
                                        @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" {{ $schedule->room_id == $room->id ? 'selected'
                                            : '' }}>
                                            {{ $room->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('room_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="hari">Hari</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="hari" @error('hari') border-red-300 @enderror>
                                        <option value="">Pilih Hari</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                        <option value="{{ $day }}" {{ $schedule->hari == $day ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('hari') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="form-label" for="waktu">Waktu</label>
                                <div class="form-control-wrap">
                                    <input type="time" class="form-control" value="{{ old('waktu', $schedule->waktu) }}"
                                        name="waktu" @error('waktu') border-red-300 @enderror>
                                </div>
                                @error('waktu') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="form-group">
                                <a href="{{ route('admin.list-jadwal.show', $schedule->department_id) }}"
                                    class="btn btn-warning">Kembali</a>
                                <button type="submit" class="btn btn-primary">Update Jadwal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('select.form-control').select2();
        });
    </script>
    @endpush
</x-main-layout>