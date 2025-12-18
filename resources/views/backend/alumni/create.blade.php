<x-main-layout>
    @section('title', 'Tambah Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Tambah Data Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Isi data alumni baru.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner">
                    <form action="{{ route('admin.alumni.store') }}" method="POST">
                        @csrf
                        <div class="row m-3">
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="student_id">Mahasiswa</label>
                                    <select name="student_id" id="student_id"
                                        class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">Pilih Mahasiswa</option>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->nim }} - {{ $student->nama_mhs
                                            }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="graduation_year">Tahun Lulus</label>
                                    <input type="number" name="graduation_year" id="graduation_year"
                                        class="form-control @error('graduation_year') is-invalid @enderror"
                                        value="{{ old('graduation_year') }}" required>
                                    @error('graduation_year')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="job_title">Jabatan</label>
                                    <input type="text" name="job_title" id="job_title"
                                        class="form-control @error('job_title') is-invalid @enderror"
                                        value="{{ old('job_title') }}">
                                    @error('job_title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="company">Perusahaan</label>
                                    <input type="text" name="company" id="company"
                                        class="form-control @error('company') is-invalid @enderror"
                                        value="{{ old('company') }}">
                                    @error('company')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="industry">Sektor Industri</label>
                                    <input type="text" name="industry" id="industry"
                                        class="form-control @error('industry') is-invalid @enderror"
                                        value="{{ old('industry') }}">
                                    @error('industry')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="salary_range">Rentang Gaji</label>
                                    <input type="text" name="salary_range" id="salary_range"
                                        class="form-control @error('salary_range') is-invalid @enderror"
                                        value="{{ old('salary_range') }}">
                                    @error('salary_range')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="further_education">Pendidikan Lanjutan</label>
                                    <input type="text" name="further_education" id="further_education"
                                        class="form-control @error('further_education') is-invalid @enderror"
                                        value="{{ old('further_education') }}">
                                    @error('further_education')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="contribution">Kontribusi</label>
                                    <textarea name="contribution" id="contribution"
                                        class="form-control @error('contribution') is-invalid @enderror">{{ old('contribution') }}</textarea>
                                    @error('contribution')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="aktif" {{ old('status')=='aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="non-aktif" {{ old('status')=='non-aktif' ? 'selected' : '' }}>
                                            Non-Aktif
                                        </option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="visibility">Visibilitas</label>
                                    <select name="visibility" id="visibility"
                                        class="form-control @error('visibility') is-invalid @enderror" required>
                                        <option value="public" {{ old('visibility')=='public' ? 'selected' : '' }}>
                                            Publik
                                        </option>
                                        <option value="internal" {{ old('visibility')=='internal' ? 'selected' : '' }}>
                                            Internal
                                        </option>
                                        <option value="private" {{ old('visibility')=='private' ? 'selected' : '' }}>
                                            Pribadi
                                        </option>
                                    </select>
                                    @error('visibility')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col mt-3">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
