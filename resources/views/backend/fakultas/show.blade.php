<x-main-layout>
    @section('title', 'Detail Fakultas: ' . $faculty->nama)

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Detail <span class="text-primary">{{
                        $faculty->nama }}</span></h3>
                <div class="nk-block-des text-soft">
                    <p>Informasi lengkap mengenai Fakultas</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('admin.faculty.index') }}"
                    class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                    <em class="icon ni ni-arrow-left me-1"></em><span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            @if (session('success'))
            <div class="alert alert-icon alert-success" role="alert">
                <em class="icon ni ni-check-circle"></em>
                <strong>{{ session('success') }}</strong>
            </div>
            @endif
            <div class="card-inner">
                <div class="row gy-4">
                    <div class="col-lg-4">
                        <div
                            class="text-center p-2 border rounded bg-light d-flex flex-column align-items-center justify-content-center">
                            <img src="{{ asset('images/foto_gedung.png') }}" alt="Logo Fakultas" class="img-fluid mb-3"
                                style="max-height: 600px;">
                            <div class="row justify-content-center text-center">
                                <h6 class="title text-uppercase text-primary fs-13px">Dekan Saat Ini</h6>
                                @if($faculty->dekanUser && $faculty->dekanUser->lecturer)
                                <div><span class="badge badge-outline-primary text-primary">
                                        {{ $faculty->dekanUser->lecturer->nama_dosen }}
                                    </span>
                                </div>
                                @else
                                <p class="text-muted">Belum ada dekan terpilih.</p>
                                @endif
                            </div>
                        </div>
                        <div
                            class="text-center border rounded bg-light d-flex justify-content-center align-items-center py-3">
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#assignDekanModal">
                                <em class="icon ni ni-user-check"></em>
                                <span>Ganti Dekan</span>
                            </button>

                            @if($faculty->dekanUser && $faculty->dekanUser->lecturer)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalProfilDekan">
                                <em class="icon ni ni-eye me-1"></em>
                                <span>Profil Dekan</span>
                            </button>
                            @endif
                        </div>

                    </div>
                    <div class="col-lg-8">
                        <div class="faculty-info">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="faculty-title mb-1">{{ $faculty->nama }}</h2>
                                    <div class="badge badge-dim bg-outline-primary rounded-pill">ID: {{ $faculty->id }}
                                    </div>
                                </div>
                            </div>

                            <div class="faculty-detail-card mb-4">
                                <h5 class="title text-uppercase text-soft fs-13px mb-3">Visi Fakultas</h5>
                                <div class="border-start border-3 border-primary ps-3">
                                    @if($faculty->visi)
                                    <p class="lead">{!! nl2br(e($faculty->visi)) !!}</p>
                                    @else
                                    <div class="alert alert-light">
                                        <em class="icon ni ni-info"></em> Visi fakultas belum diisi.
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="faculty-detail-card">
                                <h5 class="title text-uppercase text-soft fs-13px mb-3">Misi Fakultas</h5>
                                <div class="faculty-mission">
                                    @if($faculty->misi)
                                    {!! nl2br(e($faculty->misi)) !!}
                                    @else
                                    <div class="alert alert-light">
                                        <em class="icon ni ni-info"></em> Misi fakultas belum diisi.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Profil Dekan -->
    @if($faculty->dekanUser && $faculty->dekanUser->lecturer)
    <div class="modal fade" id="modalProfilDekan" tabindex="-1" aria-labelledby="modalProfilDekanLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Profil Dekan Fakultas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-12">
                                <div class="card card-bordered">
                                    <div class="card-inner-group">
                                        <div class="card-inner py-1 ">
                                            <div class="user-card user-card-s2">
                                                <div class="user-avatar lg bg-primary">
                                                    @if($faculty->dekanUser->photo)
                                                    <img src="{{ asset('storage/images/dosen/'.$faculty->dekanUser->photo ? 'admin/default.jpg' : '?') }}"
                                                        class="rounded-circle border border-2 border-primary" width="80"
                                                        height="80" alt="Foto Dekan">
                                                    @else
                                                    <div class="avatar bg-primary text-white rounded-circle border border-2 border-primary"
                                                        style="width: 80px; height: 80px; line-height: 80px; font-size: 2rem;">
                                                        {{ substr($faculty->dekanUser->lecturer->nama_dosen, 0, 1)
                                                        }}
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="user-info">
                                                    <h5>{{ $faculty->dekanUser->lecturer->nama_dosen }}</h5>
                                                    <span
                                                        class="m-0 badge badge-outline-primary text-primary">DEKAN</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-inner">
                                            <h6 class="overline-title mb-2">Details</h6>
                                            <div class="row g-3">
                                                <div class="col-sm-6 col-md-4 col-lg-12">
                                                    <span class="sub-text"><em
                                                            class="icon ni ni-user me-1"></em>NIP/NIDN</span>
                                                    <span>{{ $faculty->dekanUser->lecturer->nidn }}</span>
                                                </div>
                                                <div class="col-sm-6 col-md-4 col-lg-12">
                                                    <span class="sub-text"><em
                                                            class="icon ni ni-mail me-1"></em>Email:</span>
                                                    <span>{{ $faculty->dekanUser->lecturer->email }}</span>
                                                </div>
                                                <div class="col-sm-6 col-md-4 col-lg-12">
                                                    <span class="sub-text"><em class="icon ni ni-call me-1"></em>Nomor
                                                        Telepon:</span>
                                                    <span>{{ $faculty->dekanUser->lecturer->telp ?? '-' }}</span>
                                                </div>
                                                <div class="col-sm-6 col-md-4 col-lg-12">
                                                    <span class="sub-text"><em
                                                            class="icon ni ni-home me-1"></em>Fakultas:</span>
                                                    <span>{{ $faculty->nama }}</span>
                                                </div>
                                                <div class="col-sm-6 col-md-4 col-lg-12">
                                                    <span class="sub-text"><em
                                                            class="icon ni ni-map-pin me-1"></em>Alamat:</span>
                                                    <span>{{ $faculty->dekanUser->lecturer->address }}</span>
                                                </div>
                                            </div>
                                        </div><!-- .card-inner -->
                                        <div class="card-inner card-inner-sm">
                                            <ul class="btn-toolbar justify-center gx-1">
                                                <li><a href="mailto:{{ $faculty->dekanUser->email }}"
                                                        class="btn btn-trigger user-avatar btn-icon rounded-circle"><em
                                                            class="icon ni ni-mail"></em></a></li>
                                                <li><a href="tel:{{ $faculty->dekanUser->lecturer->telp }}"
                                                        class="btn btn-trigger user-avatar btn-icon rounded-circle"><em
                                                            class="icon ni ni-call"></em></a></li>
                                            </ul>
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
    @endif

    <!-- Assign Dean Modal -->
    <div class="modal fade" id="assignDekanModal" tabindex="-1" aria-labelledby="assignDekanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ route('admin.faculty.assign-dean', $faculty->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignDekanModalLabel">Pilih Dekan Fakultas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="lecturer_id" class="form-label">Dosen</label>
                            <select class="form-select js-select2" name="lecturer_id" required data-search="on"
                                data-placeholder="Pilih dosen">
                                <option value=""></option>
                                @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ $faculty->dekan == $lecturer->nama_dosen
                                    ?
                                    'selected' : '' }}>
                                    {{ $lecturer->nama_dosen }} ({{ $lecturer->nidn }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($faculty->dekan)
                        <div class="alert alert-info mt-3">
                            <em class="icon ni ni-info"></em> Saat ini dekan adalah: <strong>{{ $faculty->dekan
                                }}</strong>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @endpush
</x-main-layout>