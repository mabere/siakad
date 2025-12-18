<x-main-layout>
    @section('title', "Detail Program Studi - {$department->nama}")

    {{-- Header --}}
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3 align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title mb-1">Detail Program Studi</h3>
                <p class="text-muted mb-0">
                    Informasi lengkap mengenai <strong>{{ $department->nama }}</strong>.
                </p>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('dekan.departments.index') }}" class="btn btn-outline-primary">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="nk-block">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row g-4 align-items-start">
                    {{-- Kaprodi Section --}}
                    <div class="col-md-3 text-center border-end">
                        <div class="avatar avatar-xl rounded-circle mx-auto">
                            <img src="{{ $department->lecturer && $department->lecturer->user->photo ? asset('storage/images/dosen/' . $department->lecturer->user->photo) : asset('storage/images/dosen/dosen.jpg') }}"
                                class="img-fluid rounded-circle border" alt="Foto Kaprodi">
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1">
                                {{ $department->kaprodiLecturer->nama_dosen ?? 'Tidak ada Kaprodi' }}
                            </h5>
                            @if($department->kaprodiLecturer && $department->kaprodiLecturer->nidn)
                            <p class="text-muted mb-0">NIDN: {{ $department->kaprodiLecturer->nidn }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Section --}}
                    <div class="col-md-9">
                        <h4 class="text-primary fw-bold mb-2">{{ $department->nama }}</h4>
                        <p class="text-muted mb-3">
                            Jenjang: <strong>{{ $department->jenjang }}</strong>
                        </p>

                        <div class="mb-3">
                            <h6 class="fw-bold ps-4 text-left">Misi</h6>
                            <figure class="text-center">
                                <blockquote class="blockquote p-4">
                                    {{ $department->visi }}
                                </blockquote>
                            </figure>
                        </div>

                        <div class="mb-3 px-4">
                            <h6 class="fw-bold mb-1">Misi</h6>
                            <p class="mb-0 pe-5" style="text-align: justify">{!! nl2br(e($department->misi)) !!}</p>
                        </div>

                        <div class="text-muted small">
                            <div><strong>Dibuat pada:</strong> {{ $department->created_at->format('d F Y') }}</div>
                            <div><strong>Terakhir diperbarui:</strong> {{ $department->updated_at->format('d F Y')
                                }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-main-layout>