<x-main-layout>
    @section('title', 'Detail Program Studi - ' . $department->nama)

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Detail Program Studi</h3>
                <div class="nk-block-des text-soft">
                    <p>Informasi lengkap tentang program studi {{ $department->nama }}</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('admin.prodi.index') }}" class="btn btn-outline-light bg-white">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali ke Daftar Prodi</span>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="row g-4">
                    <!-- Left Column - Profile & Actions -->
                    <div class="col-lg-4">
                        <div class="profile-card bg-light rounded p-3 text-center">
                            <div class="profile-image user-avatas mb-3">
                                <img src="{{ asset('images/foto_gedung.png') }}" alt="Logo Program Studi"
                                    class="img-fluid rounded" style="max-height: 295px;width: 100%;">
                            </div>

                            <!-- Kaprodi Section -->
                            <div class="kaprodi-section border-top pt-3 mt-3">
                                @if($department->kaprodiLecturer && $department->kaprodiLecturer->user)
                                <div class="d-flex flex-column align-items-center">
                                    @if($department->kaprodiLecturer->user->photo)
                                    <img src="{{ $department->kaprodiLecturer->user->photo }}"
                                        class="rounded-circle border border-primary mb-2" width="80" height="80"
                                        alt="Foto Kaprodi">
                                    @else
                                    <div class="avatar bg-primary text-white rounded-circle mb-2"
                                        style="width: 80px; height: 80px; line-height: 80px;">
                                        {{ substr($department->kaprodiLecturer->nama_dosen, 0, 1) }}
                                    </div>
                                    @endif

                                    <h5 class="mb-1">{{ $department->kaprodiLecturer->nama_dosen }}</h5>
                                    <span class="badge bg-outline-primary p-1 text-primary mb-2">
                                        <em class="icon ni ni-user-check me-1"></em> Ketua Program Studi
                                    </span>

                                    <div class="d-flex gap-2 mb-3">
                                        <button class="btn p-2 btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#kaprodiProfileModal">
                                            Lihat Profil
                                        </button>
                                        <button class="btn p-2   btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#assignKaprodiModal">
                                            Ganti Kaprodi
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-warning">
                                    <em class="icon ni ni-alert-circle"></em>
                                    <span>Belum ada Kaprodi yang ditetapkan</span>
                                </div>
                                <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#assignKaprodiModal">
                                    <em class="icon ni ni-user-add"></em> Tetapkan Kaprodi
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Program Studi Details -->
                    <div class="col-lg-8">
                        <div class="prodi-details">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="title">{{ $department->nama }}</h2>
                                    <div class="meta">
                                        <span class="text-soft">
                                            <em class="icon ni ni-building"></em>
                                            Fakultas {{ $department->faculty->nama }}
                                        </span>
                                    </div>
                                </div>
                                <div class="badge badge-dim bg-outline-primary rounded-pill">
                                    Kode: {{ $department->code ?? '-' }}
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card card-bordered mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title with-icon mb-0">
                                                <em class="icon ni ni-book"></em> Informasi Dasar
                                            </h5>
                                        </div>
                                        <div class="card-inner">
                                            <div class="detail-item">
                                                <span class="detail-label">Akreditasi:</span>
                                                <span class="detail-value">
                                                    {{ $department->akreditasi ?? 'Belum terakreditasi' }}
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Tanggal Berdiri:</span>
                                                <span class="detail-value">
                                                    {{ $department->tgl_sk ?
                                                    \Carbon\Carbon::parse($department->tgl_sk)->format('d F Y')
                                                    : '-' }}
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">SK Pendirian:</span>
                                                <span class="detail-value">
                                                    {{ $department->sk_pendirian ?? '-' }}
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Email Prodi: </span>
                                                <span class="detail-value">
                                                    {{ $department->email ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-bordered mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title with-icon mb-0">
                                                <em class="icon ni ni-eye"></em> Visi
                                            </h5>
                                        </div>
                                        <div class="card-inner">
                                            <div class="vision-content border-start border-3 border-primary ps-3">
                                                @if ($department->visi)
                                                <p class="lead">{!! nl2br(e($department->visi)) !!}</p>
                                                @else
                                                <div class="alert alert-light">
                                                    <em class="icon ni ni-info"></em> Misi Program Studi belum diisi.
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Visi & Misi -->
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card card-bordered h-100">
                                        <div class="card-inner">
                                            <h5 class="card-title card-header bg-primary text-white with-icon mb-0">
                                                <em class="icon ni ni-target"></em> Misi
                                            </h5>
                                            <div class="mission-content lead fs-16px">
                                                {!! $department->misi ? nl2br(e($department->misi)) : '<span
                                                    class="text-soft">Belum ada misi yang ditetapkan</span>' !!}
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
    </div>

    <!-- Kaprodi Profile Modal -->
    <div class="modal fade" id="kaprodiProfileModal" tabindex="-1" aria-labelledby="kaprodiProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kaprodiProfileModalLabel">Profil Kaprodi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="profile-header text-center mb-4">
                        @if($department->kaprodiLecturer && $department->kaprodiLecturer->user &&
                        $department->kaprodiLecturer->user->photo)
                        <img src="{{ $department->kaprodiLecturer->user->photo }}"
                            class="rounded-circle border border-primary mb-2" width="80" height="80" alt="Foto Kaprodi">
                        @else
                        <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3"
                            style="width: 100px; height: 100px; line-height: 100px; font-size: 2rem;">
                            {{ $department->kaprodiLecturer ? $department->kaprodiLecturer->nama_dosen : '?' }}
                        </div>
                        @endif
                        <h5>{{ $department->kaprodiLecturer ?$department->kaprodiLecturer->nama_dosen : '?' }}</h5>
                        <div class="badge bg-primary bg-opacity-10 text-primary">
                            Ketua Program Studi: {{ $department->nama }}
                        </div>
                    </div>

                    <div class="profile-details">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">NIDN/NIP:</span>
                                    <span class="detail-value">{{ $department->kaprodiLecturer->nidn ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">Email:</span>
                                    <span class="detail-value">{{ $department->kaprodiLecturer->email ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">Telepon:</span>
                                    <span class="detail-value">{{ $department->kaprodiLecturer->telp ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">Jabatan Fungsional:</span>
                                    <span class="detail-value">{{ $department->kaprodiLecturer->jafung ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-item">
                                    <span class="detail-label">Alamat:</span>
                                    <span class="detail-value">{{ $department->kaprodiLecturer->address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Kaprodi Modal -->
    <div class="modal fade" id="assignKaprodiModal" tabindex="-1" aria-labelledby="assignKaprodiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.department.assignKaprodi', $department->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignKaprodiModalLabel">
                            {{ $department->kaprodiLecturer ? 'Ganti Kaprodi' : 'Tetapkan Kaprodi' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="head_id" class="form-label">Pilih Dosen</label>
                            <select name="head_id" id="head_id" class="form-select select2" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ $department->head_id == $lecturer->id ?
                                    'selected' : '' }}>
                                    {{ $lecturer->nama_dosen }} ({{ $lecturer->nidn ?? $lecturer->nip }})
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Pilih dosen yang akan ditetapkan sebagai Ketua Program Studi
                            </small>
                        </div>

                        @if($department->kaprodiLecturer)
                        <div class="alert alert-info mt-3">
                            <em class="icon ni ni-info"></em>
                            <span>Kaprodi saat ini: <strong>{{ $department->kaprodiLecturer->nama_dosen
                                    }}</strong></span>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $department->kaprodiLecturer ? 'Update Kaprodi' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-custom.sweet-alert />

    @push('styles')
    <style>
        .profile-card {
            position: sticky;
            top: 20px;
        }

        .detail-item {
            margin-bottom: 1rem;
        }

        .detail-label {
            display: block;
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 500;
            color: #212529;
        }

        .vision-content,
        .mission-content {
            line-height: 1.6;
        }

        .card-title.with-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .title {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .meta {
            color: #7a7f9a;
            font-size: 0.9375rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Initialize select2 if needed
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Dosen",
                dropdownParent: $('#assignKaprodiModal')
            });
        });
    </script>
    @endpush
</x-main-layout>
