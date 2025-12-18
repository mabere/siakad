<x-main-layout>
    @section('title', 'Detail Dosen')

    <div class="nk-block">
        <div class="card card-bordered shadow-lg">
            <!-- Header Section with Back Button -->
            <div class="card-header border-bottom bg-gradient-primary-to-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">Detail Dosen</h5>
                </div>
            </div>

            <div class="card-inner p-4">
                <!-- Profile Section -->
                <div class="row g-4">
                    <!-- Photo Column -->
                    <div class="col-12 col-md-4 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                @php
                                $photo = isset($dosen->user) && $dosen->user->photo ? asset('storage/images/dosen/' .
                                $dosen->user->photo) : asset('storage/images/dosen/dosen.jpg');
                                @endphp
                                <div class="avatar avatar-xxl mb-3 border-5 border-primary rounded-circle">
                                    <img src="{{ $photo }}" class="rounded-circle w-100" alt="Foto Dosen">
                                </div>
                                <div class="mt-2">
                                    <h5 class="text-primary mb-0">{{ $dosen->nama_dosen }}</h5>
                                    <div class="text-muted small">NIDN: {{ $dosen->nidn }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Column -->
                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <!-- Department Info -->
                                <div class="mb-4">
                                    <h4 class="text-primary mb-2">
                                        <em class="icon ni ni-building"></em>
                                        Program Studi
                                    </h4>
                                    <p class="h5 text-dark">{{ $dosen->department->nama }}</p>
                                </div>

                                <!-- Personal Info Grid -->
                                <div class="row g-4">
                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-primary me-3">
                                                <em class="icon ni ni-mail text-white"></em>
                                            </div>
                                            <div>
                                                <div class="text-muted small">Email</div>
                                                <div class="h6 mb-0">{{ $dosen->email }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-primary me-3">
                                                <em class="icon ni ni-call text-white"></em>
                                            </div>
                                            <div>
                                                <div class="text-muted small">Telepon</div>
                                                <div class="h6 mb-0">{{ $dosen->telp }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-primary-soft me-3">
                                                <em class="icon ni ni-calendar text-white"></em>
                                            </div>
                                            <div>
                                                <div class="text-muted small">Tanggal Lahir</div>
                                                <div class="h6 mb-0">{{ $dosen->tgl->format('j F Y') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="icon-circle bg-primary-soft me-3">
                                                <em class="icon ni ni-map-pin text-white"></em>
                                            </div>
                                            <div>
                                                <div class="text-muted small">Alamat</div>
                                                <div class="h6 mb-0">{{ $dosen->address }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @php
                    $routePrefix = Auth::user()->employee->level === 'faculty' ? 'ktu' : 'staff';
                    @endphp
                    <a href="{{ route($routePrefix . '.dosen.index') }}" class="btn btn-primary btn-md rounded-pill">
                        <em class="icon ni ni-arrow-left me-1"></em>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary-to-secondary {
            background: linear-gradient(135deg, #2b6cb0 0%, #553c9a 100%);
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar {
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
        }
    </style>
</x-main-layout>