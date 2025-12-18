<x-main-layout>
    @section('title', 'Edit Mata Kuliah MKDU: ' . $mkdu->name)

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Edit Mata Kuliah MKDU: {{ $mkdu->name }}</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('mkdu.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Terjadi Kesalahan:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('mkdu.update', $mkdu) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="curriculum_id">Pilih Kurikulum</label>
                        <select name="curriculum_id" id="curriculum_id"
                            class="form-control @error('curriculum_id') is-invalid @enderror">
                            @foreach($curricula as $curriculum)
                            <option value="{{ $curriculum->id }}" {{ old('curriculum_id', $mkdu->curricula->first()->id
                                ?? '') == $curriculum->id ? 'selected' : '' }}>
                                {{ $curriculum->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('curriculum_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="code">Kode Mata Kuliah</label>
                        <input type="text" name="code" id="code" class="form-control"
                            value="{{ old('code', $mkdu->code) }}" required>
                        @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Mata Kuliah</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $mkdu->name) }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sks">SKS</label>
                        <input type="number" name="sks" id="sks" class="form-control"
                            value="{{ old('sks', $mkdu->sks) }}" required>
                        @error('sks')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester_number">Semester</label>
                        <input type="number" name="semester_number" id="semester_number" class="form-control"
                            value="{{ old('semester_number', $mkdu->semester_number) }}" required>
                        @error('semester_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="syllabus_path">Silabus (PDF)</label>
                        <input type="file" name="syllabus_path" id="syllabus_path" class="form-control">
                        @if($mkdu->syllabus_path)
                        <small class="form-text">Silabus saat ini: <a href="{{ Storage::url($mkdu->syllabus_path) }}"
                                target="_blank">Lihat</a></small>
                        @endif
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