<x-main-layout>
    @section('title', 'KRS Mahasiswa')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Kartu Rencana Studi (KRS)</h3>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <!-- Section 1: KRS Form -->
                    {{-- Data Mahasiswa --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Data Mahasiswa</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label text-muted">Nama Lengkap</label>
                                        <div class="form-control-plaintext">{{ $student->nama_mhs }}</div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label text-muted">Nomor Induk Mahasiswa</label>
                                        <div class="form-control-plaintext">{{ $student->nim }}</div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label text-muted">Program Studi</label>
                                        <div class="form-control-plaintext">{{ $student->department->nama }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Penawaran Mata Kuliah</h5>
                        </div>
                        <div class="card-body">
                            @if($student->kelas_id === null)
                            <div class="alert alert-warning">
                                Tidak dapat mengisi KRS, karena kelas anda belum ditentukan
                            </div>
                            @else
                            @if(($schedules ?? collect())->isEmpty())
                            <div class="alert alert-info">
                                Tidak ada mata kuliah yang tersedia untuk diambil
                            </div>
                            @elseif($totalSks >= $maxSks)
                            <div class="alert alert-warning">
                                Anda telah mencapai batas maksimum SKS ({{ $maxSks }} SKS)
                            </div>
                            @else
                            <form action="{{ route('student.krs.store') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="checkAll">
                                                        <label class="custom-control-label" for="checkAll"></label>
                                                    </div>
                                                </th>
                                                <th>Kode MK</th>
                                                <th>Mata Kuliah</th>
                                                <th>SKS</th>
                                                <th>Dosen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                            <tr>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input schedule-checkbox"
                                                            id="schedule{{ $schedule->id }}" name="schedule_ids[]"
                                                            value="{{ $schedule->id }}"
                                                            data-sks="{{ $schedule->course->sks }}">
                                                        <label class="custom-control-label"
                                                            for="schedule{{ $schedule->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $schedule->course->code }}</td>
                                                <td>{{ $schedule->course->name }}</td>
                                                <td>{{ $schedule->course->sks }}</td>
                                                <td>
                                                    @foreach($schedule->lecturersInSchedule as $lecturer)
                                                    <span>{{ $lecturer->nama_dosen }}<br></span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <span class="mr-3">Total SKS terpilih: <span id="selectedSks">0</span></span>
                                    <span class="mr-3">Sisa SKS: <span id="remainingSks">{{ $maxSks - $totalSks
                                            }}</span></span>
                                    <button type="submit" class="btn btn-primary" id="submitKrs">Simpan KRS</button>
                                </div>
                            </form>

                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                            const checkAll = document.getElementById('checkAll');
                                            const scheduleCheckboxes = document.getElementsByClassName('schedule-checkbox');
                                            const selectedSksSpan = document.getElementById('selectedSks');
                                            const remainingSksSpan = document.getElementById('remainingSks');
                                            const submitButton = document.getElementById('submitKrs');
                                            const maxSks = {{ $maxSks - $totalSks }};
                                            let selectedSks = 0;

                                            function updateSksCount() {
                                                selectedSks = 0;
                                                for (let checkbox of scheduleCheckboxes) {
                                                    if (checkbox.checked) {
                                                        selectedSks += parseInt(checkbox.dataset.sks);
                                                    }
                                                }
                                                selectedSksSpan.textContent = selectedSks;
                                                remainingSksSpan.textContent = maxSks - selectedSks;

                                                // Disable submit if exceeds max SKS
                                                if (selectedSks > maxSks) {
                                                    submitButton.disabled = true;
                                                    submitButton.title = 'Total SKS melebihi batas maksimum';
                                                } else {
                                                    submitButton.disabled = false;
                                                    submitButton.title = '';
                                                }
                                            }

                                            // Check All functionality
                                            checkAll.addEventListener('change', function() {
                                                for (let checkbox of scheduleCheckboxes) {
                                                    checkbox.checked = this.checked;
                                                }
                                                updateSksCount();
                                            });

                                            // Individual checkbox change
                                            for (let checkbox of scheduleCheckboxes) {
                                                checkbox.addEventListener('change', function() {
                                                    updateSksCount();

                                                    // Update checkAll state
                                                    let allChecked = true;
                                                    for (let cb of scheduleCheckboxes) {
                                                        if (!cb.checked) {
                                                            allChecked = false;
                                                            break;
                                                        }
                                                    }
                                                    checkAll.checked = allChecked;
                                                });
                                            }
                                        });
                            </script>
                            @endpush
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Section 2: KRS Status -->
                    <div class="card card-bordered">
                        <div class="card-header">
                            <h5 class="card-title">Status KRS</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Dosen</th>
                                            <th>Status</th>
                                            <th>Disetujui Oleh</th>
                                            <th>Tanggal Disetujui</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items ?? collect() as $index => $krs)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $krs->schedule->course->code }}</td>
                                            <td>{{ $krs->schedule->course->name }}</td>
                                            <td>{{ $krs->schedule->course->sks }}</td>
                                            <td>
                                                @if($krs->schedule->lecturersInSchedule->first())
                                                {{ $krs->schedule->lecturersInSchedule->first()->nama_dosen }}
                                                @else
                                                Belum ditentukan
                                                @endif
                                            </td>
                                            <td>
                                                @if($krs->status == 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                                @elseif($krs->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                                @else
                                                <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $krs->approvedBy->nama_dosen ?? '-' }}</td>
                                            <td>{{ $krs->approved_at ? $krs->approved_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td>{{ $krs->notes ?? '-' }}</td>
                                            <td>
                                                @if($krs->status == 'pending')
                                                <form action="{{ route('student.krs.destroy', $krs->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus KRS ini?')">
                                                        <em class="icon ni ni-trash"></em>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada KRS</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($student)


                    {{-- Tampilkan history KRS hanya jika route-nya adalah krs.status --}}
                    @if(Route::currentRouteName() == 'student.krs.status')
                    <div class="card card-bordered mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Riwayat Perkuliahan</h5>
                        </div>
                    </div>
                    @foreach($krsHistory as $academicYearId => $studyPlans)
                    <div class="card card-bordered mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Semester {{ $studyPlans->first()->academicYear->ta }} ({{
                                $studyPlans->first()->academicYear->semester }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Mata Kuliah</th>
                                            <th>SKS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalSks = 0; @endphp
                                        @foreach($studyPlans as $index => $krs)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $krs->schedule->course->name }}</td>
                                            <td>
                                                {{ $krs->schedule->course->sks }}
                                                @php $totalSks += $krs->schedule->course->sks; @endphp
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="2" class="text-right"><strong>Total SKS:</strong></td>
                                            <td><strong>{{ $totalSks }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-main-layout>