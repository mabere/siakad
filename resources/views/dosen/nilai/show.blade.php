<x-main-layout>
    @section('title', 'Detail Nilai')

    <div class="container mt-4">
        <!-- Navigasi -->
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('lecturer.nilai.index') }}" class="btn btn-outline-danger" data-bs-toggle="tooltip"
                    title="Kembali">
                    <i class="icon ni ni-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('lecturer.nilai.print', $id) }}" target="_blank" class="btn btn-outline-info mx-2"
                        data-bs-toggle="tooltip" title="Cetak Nilai">
                        <i class="icon ni ni-printer"></i> Cetak
                    </a>
                    <a href="{{ route('lecturer.nilai.edit', $id) }}" class="btn btn-outline-warning"
                        data-bs-toggle="tooltip" title="Edit Nilai">
                        <i class="icon ni ni-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-book"></i> Detail Mata Kuliah</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 20%">Mata Kuliah</th>
                                <td>:</td>
                                <td><strong>{{ $jadwal->schedulable->code }}-{{ $jadwal->schedulable->name }}</strong>
                                </td>
                                <th>Jadwal Perkuliahan</th>
                                <td>:</td>
                                <td>{{ $jadwal->hari }}/{{ $jadwal->start_time->format('H:i') . '-' .
                                    $jadwal->end_time->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td>:</td>
                                <td>{{ $jadwal->kelas->name }}</td>
                                <th>Program Studi</th>
                                <td>:</td>
                                <td>{{ $jadwal->kelas->department->nama }}</td>
                            </tr>
                            <tr>
                                <th>Dosen</th>
                                <td>:</td>
                                <td>
                                    @php
                                    $lecturers = $jadwal->lecturersInSchedule->pluck('nama_dosen')->toArray();
                                    echo count($lecturers) > 2
                                    ? implode(', ', array_slice($lecturers, 0, -1)) . ', & ' . end($lecturers)
                                    : implode(' & ', $lecturers);
                                    @endphp
                                </td>
                                <th>SKS</th>
                                <td>:</td>
                                <td>{{ $jadwal->course->sks }} SKS</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Daftar Nilai Mahasiswa</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">NIM</th>
                                        <th rowspan="2" class="text-start">Nama</th>
                                        <th colspan="7">Nilai</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Kehadiran</th>
                                        <th>Keaktifan</th>
                                        <th>Tugas</th>
                                        <th>UTS</th>
                                        <th>UAS</th>
                                        <th>Nilai Akhir</th>
                                        <th>Huruf</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $index => $item)
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $item->student->nama_mhs ?? '-' }}</td>
                                        <td>{{ $item->student->nim }}</td>
                                        <td>{{ $item->attendance_total ?? 0 }}</td>
                                        <td>{{ $item->participation ?? 0 }}</td>
                                        <td>{{ $item->assignment ?? 0 }}</td>
                                        <td>{{ $item->mid ?? 0 }}</td>
                                        <td>{{ $item->final ?? 0 }}</td>
                                        <td>{{ $item->total ?? 0 }}</td>
                                        <td class="fw-bold">{{ $item->nhuruf ?? 'T/K' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-danger">Belum ada mahasiswa yang
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
        <x-custom.sweet-alert />
    </div>
</x-main-layout>
