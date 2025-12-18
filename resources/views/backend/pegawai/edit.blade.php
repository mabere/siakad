<x-main-layout>
    @section('title', 'Edit Data Pegawai')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p><a href="/admin/pegawai/">Back</a></p>
            </div>
        </div>
    </div>
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form action="{{ route('admin.pegawai.update', $employee->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Field Nama -->
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $employee->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field Department -->
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select name="department_id" id="department_id"
                            class="form-control @error('department_id') is-invalid @enderror">
                            <option value="">Pilih Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) ==
                                $department->id ? 'selected' : '' }}>
                                {{ $department->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field NIP -->
                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror"
                            value="{{ old('nip', $employee->nip) }}" required>
                        @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $employee->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field Position -->
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" name="position" id="position"
                            class="form-control @error('position') is-invalid @enderror"
                            value="{{ old('position', $employee->position) }}" required>
                        @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Pegawai</button>
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
