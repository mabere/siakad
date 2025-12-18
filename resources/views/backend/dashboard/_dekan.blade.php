<x-custom.sweet-alert />

<div class="nk-content-body">
    <div class="container py-4">
        <h3 class="mb-4">Dashboard Dekan</h3>

        {{-- Statistik Ringkas --}}
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm p-3 text-center bg-info text-white">
                    <h5><i class="icon ni ni-users"></i> Total Mahasiswa</h5>
                    <h2>{{ $totalMahasiswa }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm p-3 text-center">
                    <h5>Total Dosen</h5>
                    <h2>{{ $totalDosen }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm p-3 text-center">
                    <h5>Total Program Studi</h5>
                    <h2>{{ $totalProdi }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm p-3 text-center">
                    <h5>Permintaan Surat</h5>
                    <h2>{{ $totalSuratMasuk }}</h2>
                </div>
            </div>
        </div>

        {{-- Top IPK dan Surat --}}
        <div class="row g-2">
            {{-- Top 3 IPK --}}
            <div class="col-md-6">
                <div class="card mt-4 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5>Top 3 IPK Tertinggi ({{ $ta->ta }}/{{ $ta->semester }})</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama/NIM</th>
                                    <th>Program Studi</th>
                                    <th>IPK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topIpkStudents as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $student['name'] }}<br><small>{{ $student['nim'] }}</small></td>
                                    <td>{{ $student['department'] }}</td>
                                    <td>{{ number_format($student['ipk'] ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data IPK.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Permintaan Surat Terbaru --}}
            <div class="col-md-6">
                <div class="card mt-4 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5>Permintaan Surat Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Identitas</th>
                                    <th>Jenis Surat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suratTerbaru as $surat)
                                <tr>
                                    <td>
                                        {{ $surat->user->name }}<br>
                                        <small>
                                            @if ($surat->user->student)
                                            {{ $surat->user->student->nim }}
                                            @elseif ($surat->user->lecturer)
                                            {{ $surat->user->lecturer->nidn ?? 'N/A' }}
                                            @else
                                            Tidak tersedia
                                            @endif
                                        </small>
                                    </td>
                                    <td>{{ $surat->letterType->name }}</td>
                                    <td>
                                        @switch($surat->status)
                                        @case('approved') <span class="badge bg-success">Approved</span> @break
                                        @case('rejected') <span class="badge bg-danger">Rejected</span> @break
                                        @case('draft') <span class="badge bg-secondary">Draft</span> @break
                                        @default <span class="badge bg-warning">Pending</span>
                                        @endswitch
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada surat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('dekan.request.surat-masuk.index') }}"
                            class="btn btn-sm btn-info float-end mt-2">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kegiatan Akademik dan Pengumuman --}}
        <div class="row g-2">
            <div class="col-md-6">
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5>Kegiatan Akademik</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                        <a href="{{ route('dekan.kegiatan.akademik.index') }}" class="btn btn-primary mt-2">Lihat Semua</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5>Pengumuman Fakultas</h5>
                    </div>
                    <div class="card-body">
                        @if ($pengumuman->isEmpty())
                        <span class="text-muted">Tidak ada pengumuman.</span>
                        @else
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengumuman as $info)
                                <tr>
                                    <td>{{ $info->title }}</td>
                                    <td>
                                        <small>{{ $info->created_at->format('d M Y') }}</small>
                                        @if ($info->created_at->diffInHours() < 24) <span class="badge bg-info">
                                            Baru</span>
                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('announcements.index') }}"
                            class="btn btn-info btn-sm float-end mt-2">Selengkapnya</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: @json($kegiatans ?? []),
            eventColor: '#3788d8',
            locale: 'id',
            displayEventTime: true
        });
        calendar.render();
    });
</script>
@endpush
