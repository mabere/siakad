<x-main-layout>
    @section('title', 'Profil Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Profil Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Kelola data profil alumni Anda.</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('alumni.dashboard') }}"
                        class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>Kembali ke Dashboard</span>
                    </a>
                    <a href="{{ route('alumni.dashboard') }}"
                        class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
                        <em class="icon ni ni-arrow-left"></em>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('alumni.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="graduation_year">Tahun Lulus <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="graduation_year" id="graduation_year"
                                        class="form-control @error('graduation_year') is-invalid @enderror"
                                        value="{{ old('graduation_year', $alumni->graduation_year) }}" required>
                                    @error('graduation_year')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="job_title">Jabatan Saat Ini</label>
                                    <input type="text" name="job_title" id="job_title"
                                        class="form-control @error('job_title') is-invalid @enderror"
                                        value="{{ old('job_title', $alumni->job_title) }}"
                                        placeholder="Contoh: Software Engineer, Guru, Dokter">
                                    @error('job_title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="company">Nama Perusahaan/Institusi</label>
                                    <input type="text" name="company" id="company"
                                        class="form-control @error('company') is-invalid @enderror"
                                        value="{{ old('company', $alumni->company) }}"
                                        placeholder="Contoh: Google, SMA Negeri 1, RSUD Konawe">
                                    @error('company')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="industry">Bidang Industri</label>
                                    <input type="text" name="industry" id="industry"
                                        class="form-control @error('industry') is-invalid @enderror"
                                        value="{{ old('industry', $alumni->industry) }}"
                                        placeholder="Contoh: Teknologi, Pendidikan, Kesehatan">
                                    @error('industry')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="salary_range">Rentang Gaji (Opsional)</label>
                                    <input type="text" name="salary_range" id="salary_range"
                                        class="form-control @error('salary_range') is-invalid @enderror"
                                        value="{{ old('salary_range', $alumni->salary_range) }}"
                                        placeholder="Contoh: Rp 5jt - 8jt, > Rp 10jt">
                                    @error('salary_range')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="further_education">Pendidikan Lanjut
                                        (Opsional)</label>
                                    <input type="text" name="further_education" id="further_education"
                                        class="form-control @error('further_education') is-invalid @enderror"
                                        value="{{ old('further_education', $alumni->further_education) }}"
                                        placeholder="Contoh: S2 Universitas A, Kursus Data Science">
                                    @error('further_education')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="contribution">Kontribusi untuk Almamater/Masyarakat
                                        (Opsional)</label>
                                    <textarea name="contribution" id="contribution"
                                        class="form-control @error('contribution') is-invalid @enderror" rows="5"
                                        placeholder="Ceritakan kontribusi Anda setelah lulus...">{{ old('contribution', $alumni->contribution) }}</textarea>
                                    @error('contribution')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="visibility">Visibilitas Data Profil <span
                                            class="text-danger">*</span></label>
                                    <select name="visibility" id="visibility"
                                        class="form-select @error('visibility') is-invalid @enderror" required>
                                        <option value="public" {{ old('visibility', $alumni->visibility) == 'public' ?
                                            'selected' : '' }}>Publik (Dapat dilihat semua orang)</option>
                                        <option value="internal" {{ old('visibility', $alumni->visibility) == 'internal'
                                            ? 'selected' : '' }}>Internal (Hanya dapat dilihat oleh civitas akademika)
                                        </option>
                                        <option value="private" {{ old('visibility', $alumni->visibility) == 'private' ?
                                            'selected' : '' }}>Privat (Hanya dapat dilihat oleh Anda dan Admin)</option>
                                    </select>
                                    @error('visibility')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('alumni.dashboard') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
