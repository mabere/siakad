<x-main-layout>
    @section('title', 'Detail Mahasiswa')
    <div class="nk-block">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold text-primary mb-1">@yield('title')</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.mhs.index') }}">Mahasiswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $student->nama_mhs }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.mhs.index') }}" class="btn btn-outline-primary btn-lg rounded-pill">
                <em class="icon ni ni-arrow-left"></em> Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <!-- Photo Section -->
                    <div class="col-md-4 text-center mb-5 mb-md-0">
                        <div class="avatar avatar-xxl position-relative">
                            <img src="{{ asset($student->photo ? 'storage/images/mhs/' . $student->photo : 'storage/images/mhs/mhs.jpg') }}"
                                class="img-fluid rounded-circle border-4 border-primary shadow"
                                alt="Foto {{ $student->nama_mhs }}"
                                style="width: 200px; height: 200px; object-fit: cover;">
                            <div
                                class="badge bg-primary text-white position-absolute bottom-0 end-0 p-2 rounded-circle">
                                <em class="icon ni ni-info"></em>
                            </div>
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
                                <p class="display-5 fw-bold text-dark mb-0">{{ $student->nama_mhs }}</p>
                                <h3 class="text-primary fs-4">{{ $student->department->nama }}</h3>
                            </div>
                            <span class="">

                            </span>
                        </div>

                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-user text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Jenis Kelamin</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->gender }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-calendar text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Tempat/Tanggal Lahir</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->tgl->format('d F Y') }}</p>
                                        <p class="mb-0 fs-5 text-primary mb-0">{{ $student->tpl }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-map-pin-fill text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Alamat</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->address }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-mail text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Email</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->email }}</p>
                                        <p class="mb-0 fs-5 text-primary"></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-call text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Telepon</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->telp }}</p>
                                    </div>
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">

                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-home-fill text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Kelas</h6>
                                        <p class="mb-0 fs-5 text-primary">
                                            @if ($student->kelas)
                                            {{ $student->kelas->name }}
                                            @else
                                            <span class="text-muted">Belum ada kelas</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-calendar-fill text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Angkatan/Tahun Masuk</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->entry_year }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-check-circle text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Total SKS Lulus</h6>
                                        <p class="mb-0 fs-5 text-primary">{{ $student->total_sks }} SKS</p>
                                    </div>
                                </div>


                                <div class="d-flex align-items-start mb-3">
                                    <div class="icon-container bg-primary-soft rounded-circle p-3 me-3">
                                        <em class="icon ni ni-book text-primary fs-5"></em>
                                    </div>
                                    <div>
                                        <h6 class="text-uppercase text-muted small mb-1">Penasehat Akademik</h6>
                                        <p class="mb-0 fs-5 text-primary">
                                            @if ($student->advisor)
                                            {{ $student->advisor->nama_dosen }}
                                            @else
                                            <span class="text-muted">Belum ada PA</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                            </div>
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