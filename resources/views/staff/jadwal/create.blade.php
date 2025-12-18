<x-main-layout>
    @section('title', 'Tambah Jadwal Perkuliahan')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form @yield('title')</h5>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card-body">
            <form action="{{ route('staff.jadwal.store') }}" method="post">
                @csrf
                <input type="hidden" name="department_id" value="{{ $id }}">
                <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mata Kuliah</label>
                        <select class="form-select @error('course_id') is-invalid @enderror" name="course_id">
                            <option value="">-- Pilih Matkul --</option>
                            @foreach ($matkul as $item)
                            <option value="{{ $item->id }}" {{ old('course_id')==$item->id ? 'selected' : '' }}>
                                {{ $item->smt }} - {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select @error('hari') is-invalid @enderror">
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                            <option value="{{ $day }}" {{ old('hari')==$day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                            @endforeach
                        </select>
                        @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mulai</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                            name="start_time" value="{{ old('start_time') }}">
                        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Selesai</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time"
                            value="{{ old('end_time') }}">
                        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    @foreach([1, 2] as $num)
                    <div class="col-md-6">
                        <label class="form-label">Dosen {{ $num }}</label>
                        <select class="form-select @error('lecturer'.$num.'_id') is-invalid @enderror"
                            name="lecturer{{ $num }}_id">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($dosen as $item)
                            <option value="{{ $item->id }}" {{ old('lecturer'.$num.'_id')==$item->id ? 'selected' : ''
                                }}>
                                {{ $item->nama_dosen }}
                            </option>
                            @endforeach
                        </select>
                        @error('lecturer'.$num.'_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Minggu Awal</label>
                        <input type="number" class="form-control @error('lecturer'.$num.'_start') is-invalid @enderror"
                            min="1" max="16" name="lecturer{{ $num }}_start"
                            value="{{ old('lecturer'.$num.'_start') }}">
                        @error('lecturer'.$num.'_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Minggu Akhir</label>
                        <input type="number" class="form-control @error('lecturer'.$num.'_end') is-invalid @enderror"
                            min="1" max="16" name="lecturer{{ $num }}_end" value="{{ old('lecturer'.$num.'_end') }}">
                        @error('lecturer'.$num.'_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @endforeach
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select class="form-select @error('kelas_id') is-invalid @enderror" name="kelas_id">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $item)
                            <option value="{{ $item->id }}" {{ old('kelas_id')==$item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ruangan</label>
                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}" {{ old('room_id')==$item->id ? 'selected' : '' }}>
                                {{ $item->name }} - {{ $item->nomor }}
                            </option>
                            @endforeach
                        </select>
                        @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-4">
                    <a class="btn btn-danger" href="{{ route('staff.jadwal.index') }}">
                        <i class="icon ni ni-reply"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="icon ni ni-plus"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>