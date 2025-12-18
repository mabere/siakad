<x-main-layout>
    @section('title', 'Kehadiran')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="text-success">Presensi Tahun Akademik: {{$ta->ta}}/{{$ta->semester}}</h4>
                <p class="text-muted">*Statistik berdasarkan pertemuan terakhir yang dilaksanakan.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @if($student)
                <!-- Data Presensi -->
                <div class="card card-bordered mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Data Presensi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="text-bg-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Pertemuan Terakhir</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Alpha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($presensi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td>{{ $item['schedule']->course->name }}</td>
                                        <td>
                                            @if($item['schedule']->lecturersInSchedule->first())
                                            {{ $item['schedule']->lecturersInSchedule->first()->nama_dosen }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item['current_meeting'] }} dari 16
                                            <i class="icon ni ni-info text-success" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Statistik berdasarkan pertemuan yang sudah dilaksanakan"></i>
                                        </td>
                                        <td>{{ $item['total_hadir'] }}</td>
                                        <td>{{ $item['total_izin'] }}</td>
                                        <td>{{ $item['sakit'] }}</td>
                                        <td>{{ $item['total_alfa'] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-secondary">Tidak ada data presensi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-main-layout>