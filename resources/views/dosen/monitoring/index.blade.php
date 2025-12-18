<x-main-layout>
    @section('title', 'Monitoring Pembelajaran')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Monitoring Pembelajaran</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <ul class="nav nav-tabs mb-3" id="monitoringTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring"
                    type="button" role="tab" aria-controls="monitoring" aria-selected="true">
                    <i class="icon ni ni-calendar-check me-1"></i> Monitoring Pembelajaran
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button"
                    role="tab" aria-controls="history" aria-selected="false">
                    <i class="icon ni ni-clock-fill me-1"></i> Riwayat Monitoring
                </button>
            </li>
        </ul>

        <div class="tab-content" id="monitoringTabsContent">
            <div class="tab-pane fade show active" id="monitoring" role="tabpanel" aria-labelledby="monitoring-tab">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Jadwal</th>
                                        <th>Kelas</th>
                                        <th>Range Pertemuan</th>
                                        <th>Status Monitoring</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $schedule)
                                    @php
                                    $monitoredMeetings =
                                    $schedule->monitorings->pluck('meeting_number')->sort()->values();
                                    $meetingRange = auth()->user()->lecturer->getMeetingRange($schedule);
                                    $totalMeetings = range($meetingRange['start'], $meetingRange['end']);
                                    $remainingMeetings = array_diff($totalMeetings, $monitoredMeetings->toArray());
                                    @endphp
                                    <tr>
                                        <td><span class="fw-medium">{{ $schedule->course->name }}</span></td>
                                        <td><span class="text-nowrap">{{ $schedule->hari }}, {{ $schedule->waktu
                                                }}</span></td>
                                        <td>{{ $schedule->kelas->name }}</td>
                                        <td>{{ $meetingRange['start'] }}-{{ $meetingRange['end'] }}</td>
                                        <td>
                                            @if ($monitoredMeetings->isEmpty())
                                            <span class="badge bg-danger">Belum ada monitoring</span>
                                            @else
                                            <div class="mb-1"><small class="text-success">Sudah: Pertemuan {{
                                                    $monitoredMeetings->implode(', ') }}</small></div>
                                            @if (count($remainingMeetings) > 0)
                                            <div><small class="text-warning">Belum: Pertemuan {{ implode(', ',
                                                    $remainingMeetings) }}</small></div>
                                            @endif
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('lecturer.monitoring.create', ['schedule' => $schedule->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-plus-circle me-1"></i> Buat
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Riwayat Monitoring --}}
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Pertemuan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monitorings as $monitoring)
                                    <tr>
                                        <td>{{ $monitoring->schedule->course->name }}</td>
                                        <td>{{ $monitoring->meeting_number }}</td>
                                        <td>{{ $monitoring->monitoring_date }}</td>
                                        <td>
                                            @if ($monitoring->status === 'submitted')
                                            <span class="badge bg-primary">Diajukan</span>
                                            @elseif ($monitoring->status === 'verified')
                                            <span class="badge bg-success">Diverifikasi</span>
                                            @elseif ($monitoring->status === 'revised')
                                            <span class="badge bg-warning">Perlu Revisi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <li><a class="btn btn-primary btn-sm"
                                                    href="{{ route('lecturer.monitoring.show', $monitoring) }}"><i
                                                        class="icon ni ni-eye me-1"></i> Lihat</a></li>
                                            @if ($monitoring->status === 'revised')
                                            <li><a class="btn btn-sm btn-warning"
                                                    href="{{ route('lecturer.monitoring.edit', $monitoring) }}"><i
                                                        class="icon ni ni-pencil me-1"></i> Edit</a></li>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
