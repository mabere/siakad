<x-main-layout>
    @section('title', 'Edit Mata Kuliah: ' . $course->name)

    <div class="nk-block-head nk-block-head-sm">
        <h3 class="nk-block-title page-title">Edit Mata Kuliah: {{ $course->name }}</h3>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                @if(session('success'))
                <div class="alert alert-success alert-icon">
                    <em class="icon ni ni-check-circle"></em>
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('curriculums.courses.update', [$curriculum, $course]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Nama Mata Kuliah</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $course->name) }}" required>
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="code">Kode Mata Kuliah</label>
                                <input type="text" name="code" id="code" class="form-control"
                                    value="{{ old('code', $course->code) }}" required>
                                @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="sks">SKS</label>
                                <input type="number" name="sks" id="sks" class="form-control"
                                    value="{{ old('sks', $course->sks) }}" min="1" required>
                                @error('sks')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="semester_number">Semester</label>
                                <select name="semester_number" id="semester_number" class="form-select" required>
                                    @for($i = 1; $i <= 8; $i++) <option value="{{ $i }}" {{ old('semester_number',
                                        $course->semester_number) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                </select>
                                @error('semester_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="kategori">Kategori</label>
                                <select name="kategori" id="kategori" class="form-select" required>
                                    <option value="Wajib" {{ old('kategori', $course->kategori) === 'Wajib' ? 'selected'
                                        : '' }}>Wajib</option>
                                    <option value="Pilihan" {{ old('kategori', $course->kategori) === 'Pilihan' ?
                                        'selected' : '' }}>Pilihan</option>
                                </select>
                                @error('kategori')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="prerequisites">Prasyarat</label>
                                <select name="prerequisites[]" id="prerequisites" class="form-select" multiple>
                                    <option value="">Tidak Ada</option>
                                    @foreach($availableCourses as $prereq)
                                    <option value="{{ $prereq->id }}" {{ in_array($prereq->id, old('prerequisites',
                                        $course->prerequisites ?? [])) ? 'selected' : '' }}>
                                        {{ $prereq->name }} ({{ $prereq->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('prerequisites')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="syllabus_path">Silabus (PDF)</label>
                                <input type="file" name="syllabus_path" id="syllabus_path" class="form-control"
                                    accept=".pdf">
                                @if($course->syllabus_path)
                                <small class="form-text text-muted">
                                    Silabus saat ini: <a href="{{ Storage::url($course->syllabus_path) }}"
                                        target="_blank">Lihat</a>
                                </small>
                                @endif
                                @error('syllabus_path')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('curriculums.courses.index', $curriculum) }}"
                                class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>