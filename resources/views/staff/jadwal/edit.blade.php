<x-main-layout>
    @section('title', 'Edit Jadwal Perkuliahan')
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title card-header bg-primary text-white">
                    Form @yield('title')
                </span>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if (empty($lecturer1['id']))
            <div class="alert alert-warning"> {{ json_encode($lecturer1) }}</div>
            @endif
            @if (empty($lecturer2['id']))
            <div class="alert alert-warning">{{ json_encode($lecturer2) }}</div>
            @endif
            <div class="card-body">
                @php
                @endphp
                <form action="{{ route('staff.jadwal.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="department_id" value="{{ $schedule->department_id }}">
                    <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">
                    <input type="hidden" name="schedulable_type" id="schedulable_type"
                        value="{{ old('schedulable_type', $schedule->schedulable_type) }}">
                    <input type="hidden" name="schedulable_id" id="schedulable_id"
                        value="{{ old('schedulable_id', $schedule->schedulable_id) }}">

                    <div class="row gy-4">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="course_select">Mata Kuliah</label>
                                <div class="form-control-wrap">
                                    <select
                                        class="form-control form-select @error('schedulable_id') is-invalid @enderror"
                                        id="course_select" aria-describedby="course-error">
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        <optgroup label="Matkul Prodi">
                                            @foreach ($matkul as $item)
                                            <option value="prodi-{{ $item->id }}" {{ old('schedulable_id', $schedule->
                                                schedulable_type === 'App\\Models\\Course' && $schedule->schedulable_id
                                                == $item->id ? 'prodi-'.$item->id : '') == 'prodi-'.$item->id ?
                                                'selected' : '' }}>
                                                {{ $item->semester_number }} - {{ $item->name }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Matkul Umum (MKDU)">
                                            @foreach ($mkduCourses as $item)
                                            <option value="mkdu-{{ $item->id }}" {{ old('schedulable_id', $schedule->
                                                schedulable_type === 'App\\Models\\MkduCourse' &&
                                                $schedule->schedulable_id == $item->id ? 'mkdu-'.$item->id : '') ==
                                                'mkdu-'.$item->id ? 'selected' : '' }}>
                                                {{ $item->semester_number }} - {{ $item->name }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @error('schedulable_id')
                                    <div class="invalid-feedback" id="course-error">{{ $message }}</div>
                                    @enderror
                                    @error('schedulable_type')
                                    <div class="invalid-feedback" id="course-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="hari">Hari</label>
                                <div class="form-control-wrap">
                                    <select name="hari" id="hari"
                                        class="form-select @error('hari') is-invalid @enderror"
                                        aria-describedby="hari-error">
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                        <option value="{{ $day }}" {{ old('hari', $schedule->hari) == $day ? 'selected'
                                            : '' }}>
                                            {{ $day }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('hari')
                                    <div class="invalid-feedback" id="hari-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label class="form-label" for="start_time">Mulai</label>
                                <div class="form-control-wrap">
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        name="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}"
                                        aria-describedby="start-time-error">
                                    @error('start_time')
                                    <div class="invalid-feedback" id="start-time-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label class="form-label" for="end_time">Selesai</label>
                                <div class="form-control-wrap">
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        name="end_time"
                                        value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}"
                                        aria-describedby="end-time-error">
                                    @error('end_time')
                                    <div class="invalid-feedback" id="end-time-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Dosen 1 --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_id">Dosen 1</label>
                                <div class="form-control-wrap">
                                    <select class="form-control form-select @error('lecturer1_id') is-invalid @enderror"
                                        name="lecturer1_id" id="lecturer1_id" aria-describedby="lecturer1-error"
                                        required>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $item)
                                        <option value="{{ $item->id }}" {{ old('lecturer1_id') ?:
                                            ($lecturer1['id']==(string) $item->id ? 'selected' : '') }}>
                                            {{ $item->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('lecturer1_id')
                                    <div class="invalid-feedback" id="lecturer1-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_start">Minggu Awal</label>
                                <div class="form-control-wrap">
                                    <input type="number" min="1" max="16"
                                        class="form-control @error('lecturer1_start') is-invalid @enderror"
                                        name="lecturer1_start" id="lecturer1_start"
                                        value="{{ old('lecturer1_start') ?: $lecturer1['start'] }}"
                                        aria-describedby="lecturer1-start-error" required>
                                    @error('lecturer1_start')
                                    <div class="invalid-feedback" id="lecturer1-start-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer1_end">Minggu Akhir</label>
                                <div class="form-control-wrap">
                                    <input type="number" min="1" max="16"
                                        class="form-control @error('lecturer1_end') is-invalid @enderror"
                                        name="lecturer1_end" id="lecturer1_end"
                                        value="{{ old('lecturer1_end') ?: $lecturer1['end'] }}"
                                        aria-describedby="lecturer1-end-error" required>
                                    @error('lecturer1_end')
                                    <div class="invalid-feedback" id="lecturer1-end-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Dosen 2 --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_id">Dosen 2</label>
                                <div class="form-control-wrap">
                                    <select class="form-control form-select @error('lecturer2_id') is-invalid @enderror"
                                        name="lecturer2_id" id="lecturer2_id" aria-describedby="lecturer2-error">
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $item)
                                        <option value="{{ $item->id }}" {{ old('lecturer2_id') ?:
                                            ($lecturer2['id']==(string) $item->id ? 'selected' : '') }}>
                                            {{ $item->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('lecturer2_id')
                                    <div class="invalid-feedback" id="lecturer2-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_start">Minggu Awal</label>
                                <div class="form-control-wrap">
                                    <input type="number" min="1" max="16"
                                        class="form-control @error('lecturer2_start') is-invalid @enderror"
                                        name="lecturer2_start" id="lecturer2_start"
                                        value="{{ old('lecturer2_start') ?: $lecturer2['start'] }}"
                                        aria-describedby="lecturer2-start-error">
                                    @error('lecturer2_start')
                                    <div class="invalid-feedback" id="lecturer2-start-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="lecturer2_end">Minggu Akhir</label>
                                <div class="form-control-wrap">
                                    <input type="number" min="1" max="16"
                                        class="form-control @error('lecturer2_end') is-invalid @enderror"
                                        name="lecturer2_end" id="lecturer2_end"
                                        value="{{ old('lecturer2_end') ?: $lecturer2['end'] }}"
                                        aria-describedby="lecturer2-end-error">
                                    @error('lecturer2_end')
                                    <div class="invalid-feedback" id="lecturer2-end-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Kelas --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <div class="form-control-wrap">
                                    <select class="form-control form-select @error('kelas_id') is-invalid @enderror"
                                        name="kelas_id" id="kelas_id" aria-describedby="kelas-error">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $kls)
                                        <option value="{{ $kls->id }}" {{ old('kelas_id', $schedule->kelas_id) ==
                                            $kls->id ? 'selected' : '' }}>
                                            {{ $kls->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                    <div class="invalid-feedback" id="kelas-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="room_id">Ruangan</label>
                                <div class="form-control-wrap">
                                    <select name="room_id" id="room_id"
                                        class="form-select @error('room_id') is-invalid @enderror"
                                        aria-describedby="room-error">
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach ($ruangan as $item)
                                        <option value="{{ $item->id }}" {{ old('room_id', $schedule->room_id) ==
                                            $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} - {{ $item->nomor }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                    <div class="invalid-feedback" id="room-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mt-3">
                        <a class="btn btn-md btn-danger" href="{{ route('staff.jadwal.index') }}">
                            <em class="icon ni ni-reply"></em> Cancel
                        </a>
                        <div class="ms-1 d-flex align-items-center">
                            <button type="submit" class="btn btn-md btn-primary">
                                <em class="icon ni ni-save me-1"></em> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelector('#course_select').addEventListener('change', function () {
                const value = this.value;
                const typeInput = document.getElementById('schedulable_type');
                const idInput = document.getElementById('schedulable_id');

                if (value.startsWith('prodi-')) {
                    typeInput.value = 'App\\Models\\Course';
                    idInput.value = value.replace('prodi-', '');
                } else if (value.startsWith('mkdu-')) {
                    typeInput.value = 'App\\Models\\MkduCourse';
                    idInput.value = value.replace('mkdu-', '');
                } else {
                    typeInput.value = '';
                    idInput.value = '';
                }
            });

            document.querySelector('form').addEventListener('submit', function (e) {
                const lecturer1Start = parseInt(document.getElementById('lecturer1_start').value);
                const lecturer1End = parseInt(document.getElementById('lecturer1_end').value);
                const lecturer2Id = document.getElementById('lecturer2_id').value;
                const lecturer2Start = parseInt(document.getElementById('lecturer2_start').value);
                const lecturer2End = parseInt(document.getElementById('lecturer2_end').value);

                if (lecturer1Start >= lecturer1End) {
                    e.preventDefault();
                    alert('Minggu awal Dosen 1 harus kurang dari minggu akhir.');
                }
                if (lecturer2Id && (!lecturer2Start || !lecturer2End || lecturer2Start >= lecturer2End)) {
                    e.preventDefault();
                    alert('Jika Dosen 2 dipilih, pastikan minggu awal dan akhir diisi dengan benar.');
                }
            });
    </script>
    @endpush
</x-main-layout>
