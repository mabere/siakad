<x-main-layout>
    @section('title', 'Tambah Mata Kuliah MKDU')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Tambah Mata Kuliah MKDU</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('mkdu.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="nk-block">
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
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('mkdu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="curriculum_id">Pilih Kurikulum</label>
                        <select name="curriculum_id" id="curriculum_id" class="form-control">
                            @foreach($curricula as $curriculum)
                            <option value="{{ $curriculum->id }}">{{ $curriculum->name }}</option>
                            @endforeach
                        </select>
                        @error('curriculum_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="code">Kode Mata Kuliah</label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}"
                            required>
                        @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Mata Kuliah</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sks">SKS</label>
                        <input type="number" name="sks" id="sks" class="form-control" value="{{ old('sks') }}" required>
                        @error('sks')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester_number">Semester</label>
                        <input type="number" name="semester_number" id="semester_number" class="form-control"
                            value="{{ old('semester_number') }}" required>
                        @error('semester_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="syllabus_path">Silabus (PDF)</label>
                        <input type="file" name="syllabus_path" id="syllabus_path" class="form-control">
                        @error('syllabus_path')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('mkdu.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>