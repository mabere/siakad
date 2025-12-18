<x-main-layout>
    @section('title', 'Kartu Rencana Studi')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Kartu Rencana Studi</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Data Mahasiswa -->
                    @include('mhs.krs.partials.student-info')
                    <!-- Notifikasi Periode KRS -->

                    @if(!$isKrsActive)
                    <div class="alert alert-warning">
                        Periode penawaran KRS untuk semester {{ $ta->ta }} ({{ $ta->semester }}) telah selesai.
                        Anda
                        hanya dapat melihat riwayat KRS.
                    </div>
                    @endif
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode MK</th>
                                            <th>Nama MK</th>
                                            <th>SKS</th>
                                            <th>Dosen</th>
                                            <th>Hari</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($currentAcademicYearStudyPlans as $studyPlan)
                                        <tr>
                                            <td>{{ $studyPlan->schedule->schedulable->code ?? '-' }}</td>
                                            <td>{{ $studyPlan->schedule->schedulable->name ?? 'Mata Kuliah Tidak
                                                Diketahui'
                                                }}
                                            </td>
                                            <td>{{ $studyPlan->schedule->schedulable->sks ?? 0 }}</td>
                                            <td>
                                                @forelse($studyPlan->lecturers as $lecturer)
                                                {{ $lecturer->nama_dosen }}@if(!$loop->last), @endif
                                                @empty
                                                -
                                                @endforelse
                                            </td>
                                            <td>{{ $studyPlan->schedule->hari ?? '-' }}</td>
                                            <td>{{ ($studyPlan->schedule->start_time)->format('H:i') . '-' .
                                                ($studyPlan->schedule->end_time)->format('H:i') }}</td>
                                            <td>{{ ucfirst($studyPlan->status) }}</td>
                                            <td>
                                                @if ($studyPlan->status === 'pending' && $ta &&
                                                $ta->isKrsPeriodActive())
                                                <form action="{{ route('student.krs.destroy', $studyPlan) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini dari KRS?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                                @else
                                                -
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Anda belum mengambil mata kuliah untuk
                                                tahun
                                                akademik ini.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Tab Navigation -->
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#pengisian-krs">
                                        Pengisian KRS
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#riwayat-krs">
                                        Riwayat KRS
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pengisian-krs">
                                    @include('mhs.krs.partials.form-krs')
                                </div>
                                <div class="tab-pane fade" id="riwayat-krs">
                                    @include('mhs.krs.partials.history-krs')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
        // Inisialisasi tab
            $('.nav-tabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Debugging: Tampilkan pesan saat tab diubah
            $('.nav-tabs a').on('shown.bs.tab', function(e) {
                console.log('Tab aktif:', e.target.hash);
            });
        });
    </script>
    @endpush
    <x-custom.sweet-alert />
</x-main-layout>
