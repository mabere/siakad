<x-main-layout>
    @section('title', 'Detail Mahasiswa')
    <div class="nk-block">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold text-primary mb-1">@yield('title')</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.mahasiswa.index') }}">Mahasiswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $student->nama_mhs }}</li>
                    </ol>
                </nav>
            </div>

        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <!-- Photo Section -->
                    <div class="col-md-4 text-center mb-5 mb-md-0">
                        @php
                        $photo = isset($mhs->user) && $mhs->user->photo ? asset('storage/images/mhs/' .
                        $mhs->user->photo) : asset('storage/images/mhs/student.png');
                        @endphp
                        <div class="avatar avatar-xxl position-relative">
                            <img src="{{ $photo }}" class="img-fluid rounded-circle border-4 border-primary shadow"
                                alt="Foto {{ $student->nama_mhs }}"
                                style="width: 140px; height: 140px; object-fit: cover;">
                        </div>
                        <div class="mt-4">
                            <h6 class="fw-bold text-primary mb-1">NIM</h6>
                            <h6 class="fw-bold text-primary mb-1">{{ $student->nim }}</h6>
                        </div>
                    </div>

                    <!-- Detail Section -->
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-grow-1">
                                <p class="display-5 fw-bold text-primary mb-0">{{ $student->nama_mhs }}</p>
                                <h3 class="text-primary fs-4">{{ $student->department->nama }}</h3>
                            </div>
                            <span class="">

                            </span>
                        </div>

                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary icon-circle me-3">
                                        <em class="icon ni ni-user text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Jenis Kelamin</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->gender }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-circle bg-primary me-3">
                                        <em class="icon ni ni-calendar text-white fs-4"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Tempat/Tanggal Lahir</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->tpl }}/{{ $student->tgl->format('d F
                                            Y') }}</h6>
                                        <p class="mb-0 text-primary mb-0"></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-circle bg-primary me-3">
                                        <em class="icon ni ni-map-pin-fill text-white"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Alamat</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->address }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary icon-circle me-3">
                                        <em class="icon ni ni-mail text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Email</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->email }}</h6>
                                        <p class="mb-0 text-primary"></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary icon-circle me-3">
                                        <em class="icon ni ni-call text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Telepon</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->telp }}</h6>
                                    </div>
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary icon-circle me-3">
                                        <em class="icon ni ni-home text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Kelas</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->kelas->name }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary rounded-circle icon-circle me-3">
                                        <em class="icon ni ni-calendar-fill text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Angkatan/Tahun Masuk</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->entry_year }}</h6>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary rounded-circle icon-circle me-3">
                                        <em class="icon ni ni-check-circle text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Total SKS Lulus</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->total_sks }} SKS</h6>
                                    </div>
                                </div>


                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary rounded-circle icon-circle me-3">
                                        <em class="icon ni ni-user-round text-white fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Penasehat Akademik</h6>
                                        <h6 class="mb-0 text-primary">{{ $student->advisor->nama_dosen ?? 'Belum Ada
                                            PA' }}</h6>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mt-5 d-flex">
                            @php
                            $routePrefix = Auth::user()->employee->level === 'faculty' ? 'ktu' : 'staff';
                            @endphp
                            <a href="{{ route($routePrefix . '.mahasiswa.index') }}"
                                class="btn btn-primary btn-md rounded-pill">
                                <em class="icon ni ni-arrow-left me-1"></em>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-container {
            transition: transform 0.2s;
        }

        .icon-container:hover {
            transform: scale(1.1);
        }

        .card {
            border-radius: 1rem;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
</x-main-layout>