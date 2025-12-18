<x-main-layout>
    @section('title', 'Tambah Jadwal Kuliah')
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form action="{{ route('admin.tambah-jadwal.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $id }}">
                    <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">

                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="course_id">Mata Kuliah</label>
                                <div class="form-control-wrap">
                                    <select type="text" class="form-control form-select" :value="old('course_id')"
                                        name="course_id" @error('course_id') border-red-300 @enderror>
                                        <option value="">-- Pilih Matkul --</option>
                                        @foreach ($matkul as $item)
                                        <option value="{{ $item->id }}" {{ old('course_id')==$item->id ? 'selected' : ''
                                            }}>
                                            {{ $item->name }} (Semester {{ $item->semester_number }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('course_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_id">Dosen 1</label>
                                <div class="form-control-wrap">
                                    <select type="text" class="form-control form-select" :value="old('lecturer1_id')"
                                        name="lecturer1_id" @error('lecturer1_id') border-red-300 @enderror>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $item)
                                        <option value="{{ $item->id }}" {{ old('lecturer1_id')==$item->id ? 'selected' :
                                            '' }}>
                                            {{ $item->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('lecturer1_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <div class="form-control-wrap">
                                    <select type="text" class="form-control form-select" :value="old('kelas_id')"
                                        name="kelas_id" @error('kelas_id') border-red-300 @enderror>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}" {{ old('kelas_id')==$item->id ? 'selected' :
                                            '' }}>
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('kelas_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_start">Minggu Awal</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" min="1" max="16"
                                        :value="old('lecturer1_start')" name="lecturer1_start" @error('lecturer1_start')
                                        border-red-300 @enderror>
                                </div>
                                @error('lecturer1_start') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_end">Minggu Akhir</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" min="1" max="16"
                                        :value="old('lecturer1_end')" name="lecturer1_end" @error('lecturer1_end')
                                        border-red-300 @enderror>
                                </div>
                                @error('lecturer1_end') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="room_id">Ruangan</label>
                                <div class="form-control-wrap">
                                    <select name="room_id" id="room_id"
                                        class="form-select block w-full rounded-md @error('room_id') border-red-300 @enderror">
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach ($ruangan as $item)
                                        <option value="{{ $item->id }}" {{ old('room_id')==$item->id ? 'selected' :
                                            '' }}>
                                            {{ $item->name }}-{{ $item->nomor }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_id">Dosen 2</label>
                                <div class="form-control-wrap">
                                    <select type="text" class="form-control form-select" :value="old('lecturer2_id')"
                                        name="lecturer2_id" @error('lecturer2_id') border-red-300 @enderror>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $item)
                                        <option value="{{ $item->id }}" {{ old('lecturer2_id')==$item->id ?
                                            'selected' : '' }}>
                                            {{ $item->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('lecturer2_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="room_id">Hari</label>
                                <div class="form-control-wrap">
                                    <select name="hari" id="hari"
                                        class="form-select block w-full rounded-md @error('hari') border-red-300 @enderror">
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                        <option value="{{ $day }}" {{ old('hari')==$day ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('hari')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_start">Minggu Awal</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" min="1" max="16"
                                        :value="old('lecturer2_start')" name="lecturer2_start" @error('lecturer2_start')
                                        border-red-300 @enderror>
                                </div>
                                @error('lecturer2_start') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_end">Minggu Akhir</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" min="1" max="16"
                                        :value="old('lecturer2_end')" name="lecturer2_end" @error('lecturer2_end')
                                        border-red-300 @enderror>
                                </div>
                                @error('lecturer2_end') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="waktu">Waktu</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('waktu')" name="waktu"
                                        placeholder="08:00-10:00" @error('waktu') border-red-300 @enderror>
                                </div>
                                @error('waktu') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="start_time">Mulai</label>
                                <div class="form-control-wrap">
                                    <input type="time" class="form-control" :value="old('start_time')" name="start_time"
                                        @error('start_time') border-red-300 @enderror>
                                </div>
                                @error('start_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="end_time">Selesai</label>
                                <div class="form-control-wrap">
                                    <input type="time" class="form-control" :value="old('end_time')" name="end_time"
                                        @error('end_time') border-red-300 @enderror>
                                </div>
                                @error('end_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="waktu"></label>
                                <div class="form-control-wrap" style="margin-top: 8px">
                                    <span><a href="{{ route('admin.list-jadwal.show', $id) }}" class="btn btn-danger">
                                            Batal
                                        </a></span>

                                    <button type="submit" class="btn btn-primary">
                                        Tambah Jadwal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <!-- Form Actions -->
            </form>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lecturer1Start = document.getElementById('lecturer1_start');
            const lecturer1End = document.getElementById('lecturer1_end');
            const lecturer2Start = document.getElementById('lecturer2_start');
            const lecturer2End = document.getElementById('lecturer2_end');

            function validateWeeks(start, end) {
                if (start.value && end.value) {
                    const startWeek = parseInt(start.value);
                    const endWeek = parseInt(end.value);
                    if (endWeek < startWeek) {
                        end.setCustomValidity('Minggu akhir harus lebih besar dari minggu awal');
                    } else {
                        end.setCustomValidity('');
                    }
                }
            }

            lecturer1Start.addEventListener('change', () => validateWeeks(lecturer1Start, lecturer1End));
            lecturer1End.addEventListener('change', () => validateWeeks(lecturer1Start, lecturer1End));
            lecturer2Start.addEventListener('change', () => validateWeeks(lecturer2Start, lecturer2End));
            lecturer2End.addEventListener('change', () => validateWeeks(lecturer2Start, lecturer2End));
        });
    </script>
    @endpush
</x-main-layout>
