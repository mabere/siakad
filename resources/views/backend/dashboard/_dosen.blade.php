<x-custom.sweet-alert />

<div class="nk-content-body">
    <div class="container py-4">
        <h1 class="dashboard-title display-5 mb-4">Dashboard Dosen</h1>

        {{-- Statistik Penunjang, Publikasi, Pengabdian --}}
        <div class="row g-4 mb-4">
            @php
            $cards = [
            [
            'label' => 'Publikasi',
            'value' => $publikasi,
            'icon' => 'ni ni-book-read',
            'color' => 'success',
            'route' => route('lecturer.publication.index')
            ],
            [
            'label' => 'Pengabdian',
            'value' => $pkm,
            'icon' => 'ni ni-link-group',
            'color' => 'warning',
            'route' => route('lecturer.pkm.index')
            ],
            [
            'label' => 'Penunjang',
            'value' => $validatedAdditions,
            'icon' => 'ni ni-grid-plus',
            'color' => 'primary',
            'route' => route('lecturer.penunjang.index')
            ],
            ];
            @endphp

            @foreach ($cards as $card)
            <div class="col-12 col-md-4">
                <div class="gradient-card bg-{{ $card['color'] }} h-100 p-4">
                    <div class="d-flex align-items-center">
                        <i class="icon {{ $card['icon'] }} fa-2x text-white me-3"></i>
                        <div>
                            <h6 class="text-uppercase text-white mb-0">{{ $card['label'] }}</h6>
                            <h2 class="stat-number text-white mb-0">{{ $card['value'] }}</h2>
                        </div>
                    </div>
                    <a href="{{ $card['route'] }}"
                        class="btn bg-{{ $card['color'] }} text-white btn-md mt-3 d-block w-100">
                        Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Riwayat Mengajar + Sidebar --}}
        <div class="row g-4">
            {{-- Riwayat Mengajar --}}
            <div class="col-12 col-lg-8">
                <div class="p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Riwayat Mengajar</h5>
                        <form id="filterForm" method="GET" class="w-50">
                            <select name="academic_year_id" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">Semua Tahun Akademik</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedTa==$year->id ? 'selected' : '' }}>
                                    {{ $year->ta }}/{{ $year->semester }}
                                </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tahun/Semester</th>
                                    <th>Mata Kuliah</th>
                                    <th>Prodi</th>
                                    <th>Kelas</th>
                                    <th>SKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachingHistory as $academicYearId => $schedules)
                                @php $academicYear = $schedules->first()->academicYear; @endphp
                                @foreach($schedules as $schedule)
                                @php $usedCourse = $schedule->course ?? $schedule->mkduCourse; @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $academicYear->ta }}/{{ $academicYear->semester }}</td>
                                    <td>{{ $usedCourse->name }}</td>
                                    <td>{{ $usedCourse->department->nama ?? 'MKDU'}}</td>
                                    <td>{{ $schedule->kelas->name }}</td>
                                    <td><span class="badge bg-primary">{{ $usedCourse->sks }}</span></td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-info-circle me-2"></i>Belum ada riwayat mengajar
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    {{-- Jadwal Mengajar --}}
                    <div class="col-12">
                        <div class="gradient-card card-info p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Jadwal Mengajar</h5>
                                <span class="badge badge-accent">
                                    {{ $ta->ta }}/{{ $ta->semester }}
                                </span>
                            </div>
                            <h2 class="stat-number text-info">{{ $jadwal }}</h2>
                            <a href="/dosen/schedules" class="btn btn-info btn-sm mt-3 d-block w-100">
                                Lihat Jadwal <i class="fas fa-calendar-alt ms-2"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Evaluasi Dosen (EDOM) --}}
                    <div class="col-12">
                        <div class="gradient-card p-4">
                            <h5 class="mb-4">Evaluasi Dosen</h5>

                            {{-- Rating --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Rating Rata-rata</span>
                                    <strong class="text-primary">{{ $averageRating ?? 'N/A' }}</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ ($averageRating ?? 0) * 20 }}%"></div>
                                </div>
                            </div>

                            {{-- Kehadiran --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kehadiran</span>
                                    <strong class="text-success">{{ $attendancePercentage ?? 'N/A' }}%</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $attendancePercentage ?? 0 }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('lecturer.edom.index') }}"
                                class="btn btn-outline-primary btn-sm d-block w-100">
                                Detail EDOM <i class="fas fa-chart-bar ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</div>
