<x-main-layout>
    @section('title', 'Detail Daftar Hadir Perkuliahan')

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <x-custom.sweet-alert />
                        <h4 class="card-title text-success">Presensi Tahun Akademik: {{ $ta->ta }}/{{ $ta->semester }}
                        </h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <td>{{ $jadwal->course->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ $jadwal->kelas->name }}</td>
                                </tr>
                                <tr>
                                    <th>Program Studi</th>
                                    <td>{{ $jadwal->course->department->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Dosen</th>
                                    <td>{{ $dosen->nama_dosen }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card-body">
                    <div class="d-flex justify-content-start gap-2 m-3">
                        <a href="{{ route('lecturer.attendance.index') }}" class="btn btn-lg py-3 btn-danger"
                            data-toggle="tooltip" title="Kembali">
                            <i class="icon ni ni-reply"></i>
                        </a>
                        <a href="{{ route('lecturer.attendance.print', $id) }}" target="_blank"
                            class="btn py-3 btn-lg btn-info" data-toggle="tooltip" title="Cetak Daftar Hadir">
                            <i class="icon ni ni-printer"></i>
                        </a>
                        <a href="{{ route('lecturer.attendance.edit', $id) }}" class="btn btn-lg btn-warning py-3"
                            data-toggle="tooltip" title="Periksa Daftar Hadir">
                            <i class="icon ni ni-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <x-custom.sweet-alert />
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">NIM</th>
                                        <th rowspan="2">Nama</th>
                                        <th colspan="16">Pertemuan Ke-</th>
                                        <th rowspan="2">%</th>
                                    </tr>
                                    <tr>
                                        @foreach (range(1, 16) as $i)
                                        <th>{{ $i }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->student->nim }}</td>
                                        <td class="text-left">{{ $item->student->nama_mhs }}</td>
                                        @foreach (range(1, 16) as $i)
                                        @php
                                        $status = $item["p$i"] ?? 0; // Default to 0 if null
                                        $icons = [0 => ['ni-cross-circle', 'text-danger'], 1 => ['ni-info',
                                        'text-warning'], 2 => ['ni-check-circle', 'text-success']];
                                        [$icon, $class] = $icons[$status];
                                        @endphp
                                        <td>
                                            <span class="{{ $class }}">
                                                <i class="icon ni {{ $icon }}"></i>
                                            </span>
                                        </td>
                                        @endforeach
                                        <td>
                                            @php
                                            $totalPresent = array_sum(array_map(fn($i) => $item["p$i"] ?? 0, range(1,
                                            16)));
                                            $percentage = number_format(($totalPresent / 32) * 100, 2);
                                            @endphp
                                            {{ $percentage }}%
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="19" class="text-center text-danger">Belum ada Mahasiswa yang
                                            menawar mata kuliah.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
