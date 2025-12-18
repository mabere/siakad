<x-main-layout>
    @section('title', 'Edit Nilai')
    <div class="container mt-4">
        <!-- Header Section -->
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content text-center">
                    <h4 class="nk-block-title text-primary">Edit Daftar Nilai Mata Kuliah</h4>
                    <p class="text-muted">Perbaharui data nilai mahasiswa dengan mudah.</p>
                </div>
            </div>
        </div>

        <!-- Detail Mata Kuliah -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5>Detail Mata Kuliah</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 20%">Mata Kuliah</th>
                        <td>:</td>
                        <td>{{ $jadwal->schedulable->name }}</td>
                        <th>Program Studi</th>
                        <td>:</td>
                        <td>{{ $jadwal->schedulable->department->nama }}</td>
                    </tr>
                    <tr>
                        <th>SKS</th>
                        <td>:</td>
                        <td>{{ $jadwal->schedulable->sks }} Sks</td>
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
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>:</td>
                        <td>{{ $jadwal->kelas->name }}</td>
                        <th>Jadwal Perkuliahan</th>
                        <td>:</td>
                        <td>{{ $jadwal->hari }}/{{ $jadwal->start_time->format('H:i') . '-' .
                            $jadwal->end_time->format('H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Form Edit Nilai -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5>Edit Nilai Mahasiswa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('lecturer.nilai.update', $id) }}">
                    @csrf
                    @method('PUT')
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Kehadiran (%)</th>
                                <th>Partisipasi</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Total</th>
                                <th>Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->student->nim }}</td>
                                <td>{{ $item->student->nama_mhs }}</td>
                                <td>
                                    <input type="hidden" name="idMhs[]" value="{{ $item->student_id }}">
                                    <input type="number" name="attendance[]"
                                        value="{{ old('attendance.' . $index, $item->total_attendance) ?? 0 }}" min="0"
                                        max="100" class="form-control" required>
                                </td>
                                <td><input type="number" name="participation[]"
                                        value="{{ old('participation.' . $index, $item->participation) ?? 0 }}" min="0"
                                        max="100" class="form-control" required></td>
                                <td><input type="number" name="assignment[]"
                                        value="{{ old('assignment.' . $index, $item->assignment) ?? 0 }}" min="0"
                                        max="100" class="form-control" required></td>
                                <td><input type="number" name="mid[]"
                                        value="{{ old('mid.' . $index, $item->mid) ?? 0 }}" min="0" max="100"
                                        class="form-control" required></td>
                                <td><input type="number" name="final[]"
                                        value="{{ old('final.' . $index, $item->final) ?? 0 }}" min="0" max="100"
                                        class="form-control" required></td>
                                <td>{{ $item->total ?? 0 }}</td>
                                <td>{{ $item->nhuruf ?? 'T/K' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-info"><i class="icon ni ni-save me-1"></i>
                            Simpan</button>

                    </div>
                </form>
                <div class="mt-3">
                    @if(!$isLocked && !$isPastDeadline && $items->every('validation_status', 'pending'))
                    <form method="POST" action="{{ route('lecturer.nilai.validate.dosen', $id) }}"
                        style="display:inline; margin-top: 10px;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Yakin ingin memvalidasi nilai? Proses ini akan menunggu persetujuan prodi. Batas waktu edit: {{ $items->first()->validation_deadline?->format('d M Y') ?? 'Belum ditentukan' }}.');">Validasi
                            oleh Dosen</button>
                        <small style="display: none;">Route: {{ route('lecturer.nilai.validate.dosen', $id)
                            }}</small>
                    </form>
                    <p style="color: red; font-size: 12px;">*Validasi hanya dapat dilakukan melalui tombol di atas,
                        bukan dengan mengakses URL secara langsung.</p>
                    @elseif($isPastDeadline)
                    <p style="color: red; font-size: 12px;">Batas waktu edit telah lewat ({{
                        $items->first()->validation_deadline?->format('d M Y') }}). Silakan hubungi prodi untuk
                        perpanjangan.</p>
                    @endif

                </div>

                @if(auth()->user()->activeRole('kaprodi'))
                <div class="mt-3">
                    @if($items->every('validation_status', 'dosen_validated'))
                    <form method="POST" action="{{ route('kaprodi.nilai.approve.prodi', $id) }}"
                        style="display:inline; margin-top: 10px;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Yakin ingin menyetujui validasi?');">Setujui oleh Prodi</button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</x-main-layout>