<x-main-layout>
    @section('title', 'Detail Program Studi')
    <div class="nk-block">
        <div class="nk-block-head nk-block-head-lg mb-5">
            <div class="nk-block-between g-3 align-items-center">
                <div class="nk-block-head-content">
                    <h1 class="nk-block-title display-6 fw-bold"
                        style="background: linear-gradient(45deg, #2b6cb0, #4299e1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        {{ $department->nama }}
                    </h1>
                    <p class="lead text-muted mt-2">Program Studi {{ $department->jenjang }}</p>
                </div>
                <div class="nk-block-head-content">

                </div>
            </div>
        </div>

        <div class="card card-bordered shadow-lg rounded-3">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    @php
                    $kaprodi = $department->kaprodiLecturer;
                    $photo = isset($kaprodi->user) && $kaprodi->user->photo ? asset('storage/images/dosen/' .
                    $kaprodi->user->photo) : asset('storage/images/dosen/dosen.jpg');
                    @endphp
                    <!-- Kaprodi Section -->
                    <div class="col-md-4 text-center mb-5 mb-md-0">
                        <div class="avatar avatar-xxl rounded-circle border-4 border-primary p-1 mb-4">
                            <img src="{{ $photo }}" class="img-fluid rounded-circle shadow-sm" alt="Foto Kaprodi">
                        </div>
                        <div class="border-top pt-4">
                            <h4 class="mb-1">{{ $kaprodi ? $kaprodi->nama_dosen : 'Tidak ada Kaprodi' }}</h4>
                            <div class="badge bg-primary-soft text-primary rounded-pill">{{ $kaprodi ? $kaprodi->nidn :
                                '' }}</div>
                        </div>
                        <div class="mt-3 d-flex">
                            @php
                            $routePrefix = Auth::user()->employee->level === 'faculty' ? 'ktu' : 'staff';
                            @endphp
                            <a href="{{ route($routePrefix . '.department.index') }}"
                                class="btn btn-outline-primary btn-md rounded-pill">
                                <em class="icon ni ni-arrow-left me-1"></em>Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Program Studi Details -->
                    <div class="col-md-8">
                        <div class="ps-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-info-soft text-info fs-6 rounded-pill me-3">Visi</span>
                                <p class="lead mb-0">{{ $department->visi }}</p>
                            </div>

                            <div class="mb-5">
                                <h5 class="badge bg-info-soft text-info fs-6 rounded-pill me-3 text-uppercase mb-3">Misi
                                    Program Studi</h5>
                                <div class="bg-light rounded-3 p-4">
                                    {!! nl2br(e($department->misi)) !!}
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="border-start border-3 border-primary ps-3">
                                        <small class="text-uppercase text-muted">Dibuat</small>
                                        <p class="mb-0 fw-bold">{{ $department->created_at->translatedFormat('d F Y
                                            H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border-start border-3 border-success ps-3">
                                        <small class="text-uppercase text-muted">Diperbarui</small>
                                        <p class="mb-0 fw-bold">{{ $department->updated_at->translatedFormat('d F Y
                                            H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
