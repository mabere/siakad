<x-main-layout>
    @section('title', 'Edit Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Edit Data Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Perbarui data alumni.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner">
                    <form action="{{ route('admin.alumni.update', $alumni->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="student_id">Mahasiswa</label>
                            <input type="text" class="form-control"
                                value="{{ $alumni->student->nim }} - {{ $alumni->student->nama_mhs }}" disabled>
                            <input type="hidden" name="student_id" value="{{ $alumni->student_id }}">
                        </div>
                        <div class="form-group">
                            <label for="graduation_year">Tahun Lulus</label>
                            <input type="number" name="graduation_year" id="graduation_year"
                                class="form-control @error('graduation_year') is-invalid @enderror"
                                value="{{ old('graduation_year', $alumni->graduation_year) }}" required>
                            @error('graduation_year')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Lanjutkan dengan form field lainnya seperti job_title, company, dll., mirip dengan create.blade.php -->
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
