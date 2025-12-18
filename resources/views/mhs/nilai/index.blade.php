<x-main-layout>
    @section('title', 'Kartu Hasil Studi')
    <div class="container mt-4">
        <div class="text-center mb-4">
            <h4 class="text-success">@yield('title')</h4>
            <h5 class="text-muted">Tahun Akademik: <span class="text-primary">{{ $ta->ta }}/{{ $ta->semester }}</span>
            </h5>
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @elseif(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
            @endif
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Mahasiswa</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th scope="row">NIM</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->nim ?? 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Nama</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->nama_mhs ?? 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Kelas</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->kelas->name ?? 'Tidak tersedia' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Akademik</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th scope="row">Program Studi</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->department->nama ?? 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Dosen PA</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->advisor->nama_dosen ?? 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Angkatan</th>
                                <td>:</td>
                                <td>{{ $mahasiswa->kelas->angkatan ?? 'Tidak tersedia' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4 text-center">
            @if ($canPrintKHS)
            <a href="{{ route('student.nilai.print', $mahasiswa->id) }}" target="_blank" class="btn btn-success">
                Cetak KHS <i class="fas fa-print"></i>
            </a>
            @else
            <button type="button" class="btn btn-success"
                onclick="alert('Nilai belum bisa dicetak. Mohon menunggu nilai dikunci oleh Akademik.')">
                Cetak KHS <i class="fas fa-print"></i>
            </button>
            @endif
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-bordered text-center">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Kode MK</th>
                            <th style="text-align:left">Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Nilai Angka</th>
                            <th>Status</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-center">Nilai * SKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($grades as $index => $nilai)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $nilai->schedule->schedulable->code ?? 'N/A' }}</td>
                            <td style="text-align:left">{{ $nilai->schedule->schedulable->name ?? 'N/A' }}</td>
                            <td>{{ $nilai->schedule->schedulable->sks ?? 0 }}</td>
                            <td>{{ $nilai->total ?? 'N/A' }}</td>
                            <td>
                                @if ($nilai->validation_status === 'locked')
                                <span class="badge bg-success" data-toggle="tooltip" data-placement="top"
                                    title="Nilai sudah terkunci">
                                    {{ $nilai->nhuruf ?? 'N/A' }}
                                </span>
                                @else
                                @php
                                $statusText = match($nilai->validation_status) {
                                'pending' => 'Belum Dinilai',
                                'dosen_validated' => 'Sudah Divalidasi Dosen',
                                'kaprodi_approved' => 'Disetujui Kaprodi',
                                default => 'Tidak diketahui',
                                };
                                @endphp
                                <span class="badge bg-warning" data-toggle="tooltip" data-placement="top"
                                    title="Nilai belum terkunci">
                                    {{ $statusText }}
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($nilai->validation_status === 'locked' && $nilai->nhuruf &&
                                in_array($nilai->nhuruf, ['A', 'B', 'C', 'D']))
                                @php
                                $angka = match ($nilai->nhuruf) {
                                'A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, default => 0,
                                };
                                @endphp
                                {{ $angka }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($nilai->validation_status === 'locked' && $nilai->nhuruf &&
                                in_array($nilai->nhuruf, ['A', 'B', 'C', 'D']))
                                @php
                                $sks = $nilai->schedule->schedulable->sks ?? 0;
                                $angka = match ($nilai->nhuruf) {
                                'A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, default => 0,
                                };
                                @endphp
                                {{ $angka * $sks }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-danger">Belum ada nilai mata kuliah.</td>
                        </tr>
                        @endforelse
                        <tr>
                            <td colspan="3" class="text-right"><b>Total</b></td>
                            <td><b>{{ $totalSks }}</b></td>
                            <td colspan="3"></td>
                            <td><b>{{ $totalBobot }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="text-right font-weight-bold">Indeks Prestasi Semester (IPS):
                <span class="text-danger">*</span><span class="text-primary">
                    {{ $totalSks != 0 ? number_format($totalBobot / $totalSks, 2) : '0.00' }}
                </span>
            </h5>
            <span class="text-danger">*: Nilai lengkap baru bisa diketahui setelah divalidasi oleh Akademik</span>
        </div>
    </div>
</x-main-layout>
