<x-main-layout>
    @section('title', 'Detail Dosen')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Detail Dosen</h3>
                <div class="nk-block-des text-soft">
                    <p>Informasi lengkap tentang dosen</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="/admin/dosen" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                    <em class="icon ni ni-arrow-left"></em><span>Kembali</span>
                </a>
                <a href="/admin/dosen" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
                    <em class="icon ni ni-arrow-left"></em>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner p-0">
                <div class="row g-0">
                    <!-- Photo Column -->
                    <div class="col-lg-3 border-end">
                        <div class="p-4 text-center">
                            <div class="mb-4">
                                @if ($dosen->user && $dosen->user->photo)
                                <img src="{{ asset('storage/images/dosen/' . $dosen->user->photo) }}" class="w-100"
                                    alt="">
                                @else
                                <img src="{{ asset('storage/images/dosen/dosen.jpg') }}" class="w-100"
                                    alt="Default Photo">
                                @endif
                            </div>
                            <h4 class="mb-1">{{$dosen->nama_dosen}}</h4>
                            <div class="text-muted mb-3">{{ $dosen->department->nama }}</div>
                            <div class="badge bg-primary">
                                NIDN: {{ $dosen->nidn }}
                            </div>
                        </div>
                    </div>

                    <!-- Details Column -->
                    <div class="col-lg-9">
                        <div class="p-4">
                            <!-- Personal Information Section -->
                            <div class="mb-5">
                                <h5 class="title mb-3">Informasi Pribadi</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <div class="form-control-plaintext">{{$dosen->email}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nomor Telepon</label>
                                            <div class="form-control-plaintext">{{$dosen->telp}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <div class="form-control-plaintext">{{$dosen->tgl->format('d F Y')}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Google Scholar</label>
                                            <div class="form-control-plaintext">
                                                @if($dosen->scholar_google)
                                                <a href="https://scholar.google.com/citations?user={{ $dosen->scholar_google }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-eye me-1"></em>Buka Profile
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Alamat</label>
                                            <div class="form-control-plaintext">{{$dosen->address}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information Section -->
                            <div>
                                <h5 class="title mb-3">Informasi Tambahan</h5>
                                <div class="alert alert-light">
                                    <p class="mb-0">Informasi tambahan tentang dosen dapat ditampilkan di sini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="card-footer bg-light text-center">
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">
                        <em class="icon ni ni-arrow-left"></em> Kembali ke Daftar
                    </a>
                    <a href="{{ route('admin.dosen.edit', $dosen->id) }}" class="btn btn-primary ms-2">
                        <em class="icon ni ni-edit"></em> Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
