<x-main-layout>
    @section('title', 'Dashboard Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Dashboard Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Selamat datang kembali, <strong>{{ $user->name }}</strong>!</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-more-v"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li>
                                    <a href="{{ route('alumni.profile') }}" class="btn btn-primary">
                                        <em class="icon ni ni-edit"></em>
                                        <span>Kelola Profil</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="row g-gs">
                <!-- Personal Information Card -->
                <div class="col-md-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Informasi Pribadi</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help" data-bs-toggle="tooltip"
                                        data-bs-placement="left" title="Data diri alumni"></em>
                                </div>
                            </div>
                            <div class="mt-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Nama Lengkap</span>
                                            <span>{{ optional($user->student)->nama_mhs ?? '-' }}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">NIM</span>
                                            <span>{{ optional($user->student)->nim ?? '-' }}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Program Studi</span>
                                            <span>{{ optional(optional($user->student)->department)->nama ?? '-'
                                                }}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Fakultas</span>
                                            <span>{{
                                                optional(optional(optional($user->student)->department)->faculty)->nama
                                                ?? '-' }}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Tahun Lulus</span>
                                            <span>{{ $alumni->graduation_year ?? '-' }}</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Status Card -->
                <div class="col-md-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Status Akun</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help" data-bs-toggle="tooltip"
                                        data-bs-placement="left" title="Status akun alumni"></em>
                                </div>
                            </div>
                            <div class="mt-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Status</span>
                                            <span
                                                class="badge rounded-pill bg-{{ $alumni->status === 'aktif' ? 'success' : 'danger' }}">
                                                {{ ucfirst($alumni->status) ?? '-' }}
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-soft">Visibilitas Data</span>
                                            <span class="badge rounded-pill bg-info">
                                                {{ ucfirst($alumni->visibility) ?? '-' }}
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Career Information Card -->
                <div class="col-12">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Informasi Karir</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help" data-bs-toggle="tooltip"
                                        data-bs-placement="left" title="Informasi karir alumni"></em>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded">
                                            <div class="text-soft small mb-1">Pekerjaan Saat Ini</div>
                                            <div class="h5">{{ $alumni->job_title ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded">
                                            <div class="text-soft small mb-1">Perusahaan</div>
                                            <div class="h5">{{ $alumni->company ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded">
                                            <div class="text-soft small mb-1">Industri</div>
                                            <div class="h5">{{ $alumni->industry ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <div class="text-soft small mb-1">Rentang Gaji</div>
                                            <div class="h5">{{ $alumni->salary_range ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <div class="text-soft small mb-1">Pendidikan Lanjut</div>
                                            <div class="h5">{{ $alumni->further_education ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="col-12">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Aksi Cepat</h6>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('alumni.profile') }}" class="btn btn-primary">
                                        <em class="icon ni ni-edit"></em>
                                        <span>Perbarui Profil</span>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <em class="icon ni ni-calendar"></em>
                                        <span>Event Alumni</span>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <em class="icon ni ni-users"></em>
                                        <span>Jaringan Alumni</span>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <em class="icon ni ni-bell"></em>
                                        <span>Notifikasi</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
