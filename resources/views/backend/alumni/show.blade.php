<x-main-layout>
    @section('title', 'Detail Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Detail Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Informasi lengkap mengenai data alumni.</p>
                    </div>
                </div>
                <div class="nk-block-head-actions">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('admin.alumni.index') }}"
                                class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                                    class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            <a href="{{ route('admin.alumni.index') }}"
                                class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em
                                    class="icon ni ni-arrow-left"></em></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.alumni.edit', $alumni->id) }}" class="btn btn-primary"><em
                                    class="icon ni ni-edit"></em><span>Edit Data</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-lg">
                        <div class="align-center flex-wrap flex-md-nowrap gx-4">
                            <div class="nk-block-image mr-md-3">
                                <div class="user-avatar xl bg-primary">
                                    <span>{{ substr($alumni->student->nama_mhs, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="nk-block-content">
                                <div class="nk-block-des">
                                    <span class="overline-title">{{ $alumni->student->nim }}</span>
                                    <h4 class="title">{{ $alumni->student->nama_mhs }}</h4>
                                    <p class="text-soft">Tahun Lulus: <span class="text-base">{{
                                            $alumni->graduation_year }}</span></p>
                                    <ul class="d-flex gx-3">
                                        <li><em class="icon ni ni-check-circle-fill"></em> Status: <strong>{{
                                                $alumni->status }}</strong></li>
                                        <li><em class="icon ni ni-eye-alt-fill"></em> Visibilitas: <strong>{{
                                                $alumni->visibility }}</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-inner">
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <div class="">
                                    <h6 class="title mb-3">Informasi Personal</h6>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">NIM</span>
                                            <span class="profile-ud-value">{{ $alumni->student->nim }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Nama Lengkap</span>
                                            <span class="profile-ud-value">{{ $alumni->student->nama_mhs }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Tahun Lulus</span>
                                            <span class="profile-ud-value">{{ $alumni->graduation_year }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Status</span>
                                            <span class="profile-ud-value">{{ $alumni->status }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Visibilitas</span>
                                            <span class="profile-ud-value">{{ $alumni->visibility }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="">
                                    <h6 class="title mb-3">Informasi Karir & Pendidikan Lanjut</h6>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Jabatan Terakhir</span>
                                            <span class="profile-ud-value">{{ $alumni->job_title ?? 'Tidak diisi'
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Perusahaan</span>
                                            <span class="profile-ud-value">{{ $alumni->company ?? 'Tidak diisi'
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Sektor Industri</span>
                                            <span class="profile-ud-value">{{ $alumni->industry ?? 'Tidak diisi'
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Rentang Gaji</span>
                                            <span class="profile-ud-value">{{ $alumni->salary_range ?? 'Tidak diisi'
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Pendidikan Lanjutan</span>
                                            <span class="profile-ud-value">{{ $alumni->further_education ?? 'Tidak
                                                diisi' }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Kontribusi</span>
                                            <span class="profile-ud-value">{{ $alumni->contribution ?? 'Tidak diisi'
                                                }}</span>
                                        </div>
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
