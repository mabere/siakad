<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Dashboard</h3>
                <div class="nk-block-des text-soft">
                    <p>Selamat datang di Dashboard Staff Program Studi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="row g-gs">
            <!-- Mahasiswa -->
            <x-dashboard.stat-box title="Total Mahasiswa" :value="$mahasiswaCount" url="/staff/mahasiswa"
                color="danger" />

            <!-- Dosen -->
            <x-dashboard.stat-box title="Total Dosen" :value="$dosenCount" url="/staff/dosen" color="primary" />

            <!-- Mata Kuliah -->
            <x-dashboard.stat-box title="Mata Kuliah" :value="$courseCount" url="#" color="info" />

            <!-- Jadwal Terbaru -->
            <div class="col-md-6 col-xxl-4">
                <div class="card card-bordered card-full">
                    <div class="card-inner bg-primary border-bottom">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title text-white">Jadwal Perkuliahan Terbaru</h6>
                            </div>
                            <div class="card-tools">
                                <ul class="card-tools-nav">
                                    <li>
                                        <a href="{{ route('staff.jadwal.index') }}"
                                            class="btn btn-icon px-1 text-white bg-outline-info bg-info">
                                            Lihat Semua Jadwal
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <ul class="nk-activity">
                        @forelse ($latestJadwal as $item)
                        <li class="nk-activity-item">
                            <div class="nk-activity-media">
                                <div class="d-flex flex-column">
                                    <strong>Dosen:</strong>
                                    <ul class="list-unstyled mb-0">
                                        @forelse ($item->lecturersInSchedule as $lecturer)
                                        <li>{{ $lecturer->nama_dosen }}</li>
                                        @empty
                                        <li><em>Belum ada dosen</em></li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-activity-data">
                                <div class="label fw-bold">{{ $item->course->name ?? $item->mkduCourse->name }}</div>
                                <div class="text-muted small">
                                    Hari: {{ $item->hari }} <br>
                                    Pukul: {{ $item->waktu }}
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="nk-activity-item">
                            <div class="nk-activity-data">
                                <span class="text-muted">Belum ada jadwal tersedia</span>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Validasi Nilai -->
            <div class="col-md-6 col-xxl-3">
                <div class="card card-bordered card-full">
                    <div class="card-inner-group">
                        <div class="card-inner bg-primary border-bottom">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title text-white">Perubahan Status Validasi Nilai</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="card-tools-nav">
                                        <li>
                                            <a href="{{ route('staff.nilai.validasi') }}"
                                                class="btn btn-icon px-1 text-white bg-outline-info bg-info">Lihat
                                                Semua</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner card-inner-md p-0">
                            @if ($recentValidationChanges->isEmpty())
                            <div class="p-3 text-center">
                                <p>Belum ada perubahan status validasi nilai.</p>
                            </div>
                            @else
                            <table class="table table-striped table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="p-2">Kode</th>
                                        <th class="p-2">Mata Kuliah</th>
                                        <th class="p-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentValidationChanges as $schedule)
                                    @php
                                    $usedCourse = $schedule->course ?? $schedule->mkduCourse;

                                    $status = $schedule->grades->isNotEmpty() ?
                                    $schedule->grades->first()->validation_status : 'Tidak ada data';
                                    $statusClass = match ($status) {
                                    'pending' => 'danger',
                                    'dosen_validated' => 'warning',
                                    'kaprodi_approved' => 'info',
                                    'locked' => 'success',
                                    default => 'secondary'
                                    };

                                    $displayStatus = match ($status) {
                                    'pending' => 'Dosen Belum Validasi',
                                    'dosen_validated' => 'Divalidasi Dosen',
                                    'kaprodi_approved' => 'Divalidasi Prodi',
                                    'locked' => 'Terkunci',
                                    default => 'Belum ada data'
                                    };
                                    @endphp

                                    <tr>
                                        <td class="p-3">{{ $schedule->id }} - {{ $usedCourse->code ?? '-' }}</td>
                                        <td class="p-3">{{ $usedCourse->name ?? $usedCourse->name ?? 'Belum ditentukan'
                                            }}</td>
                                        <td class="p-3">
                                            <span class="badge bg-{{ $statusClass }}">{{ $displayStatus }}</span>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
