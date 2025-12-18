<x-main-layout>
    @section('title', 'Jadwal Perkuliahan')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Daftar jadwal kuliah semester aktif.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                                @endif
                                @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="text-bg-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Pukul</th>
                                                <th>Mata Kuliah</th>
                                                <th>SKS</th>
                                                <th>Dosen</th>
                                                <th>Ruangan</th>
                                                <th>Status KRS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($schedules as $day => $daySchedules)
                                            <tr>
                                                <td colspan="7"
                                                    class="table-group-divider fw-bold bg-light text-primary">
                                                    {{ $day }}
                                                </td>
                                            </tr>
                                            @php $dayIteration = 1; @endphp
                                            @foreach($daySchedules as $schedule)
                                            <tr>
                                                <td>{{ $dayIteration++ }}.</td>
                                                <td>
                                                    @if($schedule->start_time && $schedule->end_time)
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $schedule->currentCourse->name ?? 'N/A' }}
                                                    <div class="text-muted small">{{ $schedule->currentCourse->code ??
                                                        'N/A' }}</div>
                                                </td>
                                                <td>{{ $schedule->sks_value ?? 0 }}</td>
                                                <td>
                                                    @forelse ($schedule->lecturersInSchedule as $lecturer)
                                                    <div>{{ $lecturer->nama_dosen }}</div>
                                                    @empty
                                                    -
                                                    @endforelse
                                                </td>
                                                <td>{{ $schedule->room->name ?? 'Tidak Diketahui' }}</td>
                                                <td>
                                                    @if($schedule->study_plan_status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @elseif($schedule->study_plan_status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @elseif($schedule->study_plan_status)
                                                    <span class="badge bg-secondary">{{
                                                        ucfirst($schedule->study_plan_status) }}</span>
                                                    @else
                                                    <span class="badge bg-danger">Tidak Diketahui</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-warning">
                                                    Tidak ada jadwal kuliah yang tersedia
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($ta)
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <p class="mb-0"><strong>Tahun Akademik:</strong> {{ $ta->ta }}</p>
                                        <p class="mb-0"><strong>Semester:</strong> {{ $ta->semester }}</p>
                                        <p class="mb-0"><strong>Total SKS:</strong> {{ $totalSks }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
