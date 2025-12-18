<x-main-layout>
    @section('title', 'Edit Kurikulum')

    <div class="nk-block-head nk-block-head-sm">
        <h3 class="nk-block-title page-title">Edit Kurikulum: {{
            \App\Helpers\CurriculumHelper::formatCurriculumName($curriculum) }}</h3>
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

                <form action="{{ route('curriculums.update', $curriculum) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="department_id">Program Studi</label>
                                <select name="department_id" id="department_id" class="form-select" required>
                                    @foreach(\App\Models\Department::all() as $department)
                                    <option value="{{ $department->id }}" {{ $curriculum->department_id ==
                                        $department->id ? 'selected' : '' }}>
                                        {{ $department->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="academic_year_id">Tahun Akademik</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                    @foreach(\App\Models\AcademicYear::all() as $academicYear)
                                    <option value="{{ $academicYear->id }}" {{ $curriculum->academic_year_id ==
                                        $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->ta }} {{ $academicYear->semester }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Nama Kurikulum</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $curriculum->name) }}" required>
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="draft" {{ old('status', $curriculum->status) === 'draft' ? 'selected'
                                        : '' }}>Draft</option>
                                    <option value="active" {{ old('status', $curriculum->status) === 'active' ?
                                        'selected' : '' }}>Active</option>
                                    <option value="archived" {{ old('status', $curriculum->status) === 'archived' ?
                                        'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Deskripsi</label>
                                <textarea name="description" id="description"
                                    class="form-control">{{ old('description', $curriculum->description) }}</textarea>
                                @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
