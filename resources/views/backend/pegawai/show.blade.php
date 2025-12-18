<x-main-layout>
    @section('title', 'Detail Pegawai')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3 align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <p class="text-muted">Informasi lengkap Pegawai</p>
            </div>
            <div class="nk-block-head-content">
                <a href="/admin/pegawai"></a>
            </div>
        </div>
    </div>
    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img src="{{ asset('images/staff/' . $pegawai->photo) }}" alt="{{ $pegawai->nama }}"
                                    class="rounded-circle img-thumbnail" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-0"><u>@yield('title')</u></h5>
                                <h6 class="text-white mb-0">{{ $pegawai->nama }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Program Studi:</span>
                                <span>
                                    @if ($pegawai->department_id)
                                    {{ $pegawai->department->nama }}
                                    @else
                                    KTU
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">NIP:</span>
                                <span>{{ $pegawai->nip }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Jabatan:</span>
                                <span>{{ $pegawai->position }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Alamat Email:</span>
                                <span>{{ $pegawai->email }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-light text-center py-3">
                        <a href="/admin/pegawai" class="btn btn-warning">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>