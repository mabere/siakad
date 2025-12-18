<x-main-layout>
    @section('title', 'Detail Daftar Hadir Perkuliahan')

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <x-custom.sweet-alert />
                        <h4 class="card-title text-success">Presensi Tahun Akademik: {{ $ta->ta ?? 'N/A' }}/{{
                            $ta->semester ?? 'N/A' }}
                        </h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <td>
                                        {{ $jadwal->course_name ?? 'N/A' }}
                                        ({{ $jadwal->course_code ?? 'N/A' }})
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ $jadwal->kelas->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Program Studi</th>
                                    <td>
                                        @if($jadwal->schedulable_type === 'App\Models\Course' &&
                                        $jadwal->course->department)
                                        {{ $jadwal->course->department->nama }}
                                        @else
                                        MKDU / Tidak Berlaku
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dosen</th>
                                    <td>{{ $dosen->nama_dosen ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Hari/Waktu</th>
                                    <td>{{ $jadwal->hari . '/' . $jadwal->start_time->format('H:i') . '-' .
                                        $jadwal->end_time->format('H:i') }}</td>
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
                        <a href="{{ route('lecturer.attendance.edit', $jadwal->id) }}"
                            class="btn btn-lg btn-warning py-3" data-toggle="tooltip" title="Isi Daftar Hadir">
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
                                        <td>{{ $item->student->nim ?? 'N/A' }}</td>
                                        <td class="text-left">{{ $item->student->nama_mhs ?? 'N/A' }}</td>
                                        @foreach (range(1, 16) as $i)
                                        @php
                                        $attendanceDetail = $item->attendanceDetails->where('meeting_number',
                                        $i)->first();
                                        $status = $attendanceDetail ? $attendanceDetail->status : 'Tanpa Keterangan';
                                        $icons = [
                                        'Tanpa Keterangan' => ['ni-cross-circle', 'text-danger'],
                                        'Izin' => ['ni-info', 'text-warning'],
                                        'Sakit' => ['ni-info', 'text-warning'],
                                        'Hadir' => ['ni-check-circle', 'text-success'],
                                        ];
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
                                            $totalPresent = $item->attendanceDetails->where('status', 'Hadir')->count();
                                            $percentage = number_format(($totalPresent / 16) * 100, 2);
                                            @endphp
                                            {{ $percentage }}%
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="19" class="text-center text-danger">Belum ada Mahasiswa yang
                                            menawar mata kuliah atau data presensi belum diisi.</td>
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
