<x-main-layout>
    @section('title', 'Detail Kurikulum')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"> {{
                    \App\Helpers\CurriculumHelper::formatCurriculumName($curriculum) }}</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('curriculums.courses.index', $curriculum) }}" class="btn btn-primary">
                    <em class="icon ni ni-eye me-1"></em> Daftar Mata Kuliah
                </a>
                <a href="{{ route('curriculums.edit', $curriculum) }}" class="btn btn-outline-primary">
                    <em class="icon ni ni-edit me-1"></em> Edit Kurikulum
                </a>
                <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
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
                <div class="nk-block">
                    <h5>Informasi Kurikulum</h5>
                    <p><strong>Nama:</strong> {{ $curriculum->name }}</p>
                    <p><strong>Program Studi:</strong> {{ $curriculum->department->nama }}</p>
                    <p><strong>Tahun Akademik:</strong> {{ $curriculum->academicYear->ta }} {{
                        $curriculum->academicYear->semester }}</p>
                    <p><strong>Status:</strong>
                        <span
                            class="badge bg-{{ $curriculum->status === 'active' ? 'success' : ($curriculum->status === 'draft' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($curriculum->status) }}
                        </span>
                    </p>
                    <p><strong>Deskripsi:</strong> {{ $curriculum->description ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>