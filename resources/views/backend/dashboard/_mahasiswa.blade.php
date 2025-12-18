<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Dashboard Mahasiswa</h3>
        </div>
    </div>
</div>

{{-- Profil Mahasiswa --}}
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-lg-12">
            <div class="card card-bordered">
                <div class="card-header" style="background: linear-gradient(135deg, #4CAF50, #81C784);">
                    <h5 class="card-title text-white">PROFILE MAHASISWA</h5>
                </div>
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-lg-3 text-center">
                            @if(Auth::user()->photo)
                            <img src="{{ asset('storage/images/mhs/' . Auth::user()->photo) }}" alt="Foto Mahasiswa"
                                class="img-fluid rounded-circle" style="max-width: 150px;">
                            @else
                            <div class="border rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 150px; height: 150px; background-color: #f0f0f0;">
                                <span class="text-muted">Foto</span>
                            </div>
                            @endif
                            <a class="btn btn-sm bg-purple text-white mt-3" href="{{ route('profile') }}">Ubah
                                Profile</a>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Nama:</strong> {{ $student->nama_mhs }}</li>
                                        <li class="list-group-item"><strong>NIM:</strong> {{ $student->nim }}</li>
                                        <li class="list-group-item"><strong>Jenis Kelamin:</strong> {{ $student->gender
                                            ?? 'Belum diisi' }}</li>
                                        <li class="list-group-item"><strong>Tempat/Tanggal Lahir:</strong> {{
                                            $student->tpl ?? '-' }}/{{ $student->tgl ? date('d F Y',
                                            strtotime($student->tgl)) : '-' }}</li>
                                        <li class="list-group-item"><strong>Tahun Masuk:</strong> {{
                                            $student->entry_year }}/@if($student->entry_semester == 1)Baru@else Pindahan
                                            @endif</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Program Studi:</strong> {{
                                            $student->department->nama ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Kelas:</strong> {{ $student->kelas->name ??
                                            '-' }}</li>
                                        <li class="list-group-item"><strong>Alamat:</strong> {{ $student->address ?? '-'
                                            }}</li>
                                        <li class="list-group-item"><strong>Telepon:</strong> {{ $student->telp ?? '-'
                                            }}</li>
                                        <li class="list-group-item"><strong>Email:</strong> {{ $student->email ?? '-' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Informasi Akademik, EDOM, Akses Cepat --}}
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-md-4">
            <x-chart.card title="Informasi Akademik" color="info">
                <ul class="list list-sm list-checked">
                    <li><strong>IPK:</strong> {{ number_format($ipk ?? 0, 2) }}</li>
                    <li><strong>SKS Diperoleh:</strong> {{ $student->total_sks }}</li>
                    <li><strong>Status:</strong>
                        <span class="badge bg-success text-white text-capitalize">{{ $status }}</span>
                    </li>
                    <li><strong>Semester:</strong> {{ $currentSemester }}</li>
                    <li><strong>Dosen PA:</strong> {{ $student->advisor->nama_dosen ?? '-' }}</li>
                </ul>
            </x-chart.card>
        </div>

        <div class="col-md-4">
            <x-chart.card title="Akses Cepat" color="primary">
                <ul class="list list-sm list-checked">
                    <li><a href="/mhs/jadwal">Lihat Jadwal</a></li>
                    <li><a href="/mhs/presensi">Info Presensi</a></li>
                    <li><a href="/mhs/krs">KRS Semester</a></li>
                    <li><a href="/mhs/nilai">Nilai Semester</a></li>
                    @if($student->total_sks > 120)
                    <li><a href="/mhs/thesis/supervision">Bimbingan Skripsi</a></li>
                    @endif
                </ul>
            </x-chart.card>
        </div>

        <div class="col-md-4">
            <x-chart.card title="Kuesioner EDOM" color="warning">
                <div class="d-flex align-items-start">
                    <a href="/mhs/edom">
                        <img src="{{ asset('images/product/5.png') }}" alt="EDOM" width="35%" class="img-thumbnail">
                    </a>
                    @if($hasIncompleteEdom)
                    <div class="alert alert-warning ms-2 mt-1">
                        Ada {{ $incompleteEdomCount }} evaluasi belum kamu isi.
                        <a href="/mhs/edom" class="btn btn-sm btn-secondary mt-2">Silakan Isi</a>
                    </div>
                    @endif
                </div>
            </x-chart.card>
        </div>
    </div>
</div>

<div class="nk-block">
    <div class="row g-gs">
        <div class="col-md-4">
            <x-chart.card title="Kegiatan Akademik" color="secondary">
                <ul class="list list-sm list-checked">
                    <li><strong>Pengumuman:</strong> <a href="{{ route('announcements.index') }}">Baca</a></li>
                    <li><strong>Kalender:</strong> <a href="{{ route('calendar.index') }}">Selengkapnya</a> </li>
                    <li><strong>Ujian Mendatang:</strong> - </li>
                </ul>
            </x-chart.card>
        </div>

        <div class="col-md-4">
            <x-chart.card title="Tautan Cepat" color="dark">
                <ul class="list list-sm list-checked">
                    <li><a href="{{ route('profile') }}">Profil Mahasiswa</a></li>
                    <li><a href="{{ route('profile') }}">Pengaturan Akun</a></li>
                    <li><a href="#">Bantuan</a></li>
                </ul>
            </x-chart.card>
        </div>

        <div class="col-md-4">
            <x-chart.card title="Mata Kuliah" color="info">
                <ul class="list list-sm list-checked">
                    @foreach ($studyPlans as $studyPlan)
                    @php
                    $usedCourse = optional($studyPlan->schedule)->current_course ??
                    optional($studyPlan->mkduCourse)->current_course;
                    @endphp
                    <li>{{ $usedCourse?->name ?? 'Mata kuliah tidak ditemukan' }}</li>
                    @endforeach
                </ul>
            </x-chart.card>
        </div>

        <div class="col-md-4">
            <div class="card card-bordered">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Grafik Perkembangan IPK</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px;">
                        <canvas id="ipkChart" style="max-height: 230px;"></canvas>
                        @if(empty($ipkData) || count($ipkData) === 0)
                        <div class="text-muted mt-2 text-center">Belum ada data IPK</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('ipkChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($ipkLabels),
                datasets: [{
                    label: 'IPK',
                    data: @json($ipkData),
                    borderColor: '#0f0f0f',
                    backgroundColor: 'rgba(53, 47, 219, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4.0
                    }
                }
            }
        });
    });
</script>
@endpush
