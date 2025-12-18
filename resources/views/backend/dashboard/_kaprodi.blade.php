<x-custom.sweet-alert />

<div class="nk-content-body">

    {{-- Header --}}
    <div class="card-header bg-secondary text-white py-2 mb-4">
        <h4>Selamat Datang, {{ auth()->user()->name }} - Kaprodi: {{ $department }}</h4>
    </div>

    {{-- Statistik Box --}}
    <div class="row g-3 mb-4">
        @php
        $boxes = [
        ['title' => 'Mahasiswa Aktif', 'value' => $totalMahasiswa, 'color' => 'primary', 'route' =>
        route('kaprodi.mahasiswa.index')],
        ['title' => 'Jumlah Kelas', 'value' => $totalKelas, 'color' => 'success', 'route' =>
        route('kaprodi.kelas.index')],
        ['title' => 'Jumlah Dosen', 'value' => $totalDosen, 'color' => 'info', 'route' => route('kaprodi.dosen.index')],
        ['title' => 'Mahasiswa Tanpa Kelas', 'value' => $mahasiswaTanpaKelas, 'color' => 'warning', 'route' => null],
        ];
        @endphp

        @foreach ($boxes as $box)
        <div class="col-md-3">
            <div class="card text-white bg-{{ $box['color'] }}">
                <div class="card-body">
                    <h6 class="card-title">{{ $box['title'] }}</h6>
                    <h3>{{ $box['value'] }}</h3>
                </div>
                @if ($box['route'])
                <a href="{{ $box['route'] }}" class="btn btn-light btn-sm">Lihat</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel IPK dan Surat Terbaru --}}
    <div class="row mb-4">
        {{-- Top 3 IPK --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Top 3 Mahasiswa Berdasarkan IPK ({{ $ta->ta }}/{{ $ta->semester }})</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topIpkStudents as $i => $mhs)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $mhs['nim'] }}</td>
                                <td>{{ $mhs['name'] }}</td>
                                <td>{{ $mhs['ipk'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data IPK.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Surat Terbaru --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Surat Ajuan Terbaru</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIM/NIDN</th>
                                <th>Nama</th>
                                <th>Jenis Surat</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suratTerbaru as $i => $surat)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ $surat->user->student->nim ?? $surat->user->lecturer->nidn ?? 'N/A' }}
                                </td>
                                <td>{{ $surat->user->name }}</td>
                                <td>{{ $surat->letterType->name }}</td>
                                <td>{{ $surat->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada surat ajuan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <a href="{{ route('kaprodi.request.surat-masuk.index') }}"
                        class="btn btn-primary btn-sm float-end">Lihat Semua</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Data Statistik</h6>
                </div>
                <div class="card-body">
                    <canvas id="statsChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Distribusi IPK Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <canvas id="ipkDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (!empty($notifikasi))
    <div class="alert alert-warning">
        <ul class="mb-0">
            @foreach ($notifikasi as $pesan)
            <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tabel Kelas --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Daftar Kelas/Angkatan Prodi {{ $department }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Dosen Wali</th>
                                <th>Angkatan</th>
                                <th>Jumlah Mahasiswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas as $k)
                            <tr>
                                <td>{{ $k->name }}</td>
                                <td>{{ $k->lecturer->nama_dosen ?? 'Belum Ditugaskan' }}</td>
                                <td>{{ $k->angkatan }}</td>
                                <td>{{ $k->students->count() }}</td>
                                <td>
                                    <a href="{{ route('kaprodi.kelas.show', $k->id) }}"
                                        class="btn btn-sm btn-primary">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kelas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('statsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Mahasiswa', 'Kelas', 'Dosen', 'Tanpa Kelas', 'Surat Masuk'],
            datasets: [{
                label: 'Statistik',
                data: [
                    {{ $totalMahasiswa }},
                    {{ $totalKelas }},
                    {{ $totalDosen }},
                    {{ $mahasiswaTanpaKelas }},
                    {{ $totalSuratMasuk }}
                ],
                backgroundColor: ['#36A2EB','#28a745','#17a2b8','#ff9f40','#9966ff'],
                borderColor: ['#36A2EB','#28a745','#17a2b8','#ff9f40','#9966ff'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    new Chart(document.getElementById('ipkDistributionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['1.0-2.0', '2.0-3.0', '3.0-4.0'],
            datasets: [{
                label: 'Distribusi IPK',
                data: [
                    {{ $ipkDistribution['1.0-2.0'] }},
                    {{ $ipkDistribution['2.0-3.0'] }},
                    {{ $ipkDistribution['3.0-4.0'] }}
                ],
                backgroundColor: ['#ff6384','#36a2eb','#4bc0c0'],
                borderColor: ['#ff6384','#36a2eb','#4bc0c0'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Mahasiswa' }
                },
                x: {
                    title: { display: true, text: 'Rentang IPK' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
