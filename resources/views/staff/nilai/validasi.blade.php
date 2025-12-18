<x-main-layout>
    @section('title', 'Validasi Nilai Mata Kuliah')

    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                </div>
            </div>
            <div class="card-header bg-white py-3">
                <div class="row align-items-center justify-content-between">
                    <div class="col-12">
                        <div class="card card-bordered mb-3">
                            <div class="card-header bg-secondary">
                                <h5 class="m-0 font-weight-bold text-white">Daftar Nilai Mata Kuliah Yang
                                    Membutuhkan Validasi Prodi</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Jadwal</th>
                                                <th>Mata Kuliah</th>
                                                <th>Kelas</th>
                                                <th>Status Validasi</th>
                                                <th>Batas Waktu</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($schedules as $index => $schedule)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $schedule->id }}-{{ $schedule->schedulable->code }}</td>
                                                <td>{{ $schedule->schedulable->name ?? 'Belum ditentukan' }}</td>
                                                <td>{{ $schedule->kelas->name ?? 'Belum ditentukan' }}</td>
                                                <td>
                                                    @php
                                                    $status = $schedule->grades->isNotEmpty() ?
                                                    $schedule->grades->first()->validation_status : 'Tidak ada data';
                                                    $displayStatus = '';
                                                    $statusClass = '';
                                                    switch ($status) {
                                                    case 'pending':
                                                    $displayStatus = 'Dosen Belum Validasi';
                                                    $statusClass = 'danger';
                                                    break;
                                                    case 'dosen_validated':
                                                    $displayStatus = 'Divalidasi Dosen';
                                                    $statusClass = 'warning';
                                                    break;
                                                    case 'kaprodi_approved':
                                                    $displayStatus = 'Divalidasi Prodi';
                                                    $statusClass = 'info';
                                                    break;
                                                    case 'locked':
                                                    $displayStatus = 'Terkunci';
                                                    $statusClass = 'success';
                                                    break;
                                                    default:
                                                    $displayStatus = 'Belum ada data';
                                                    $statusClass = 'secondary';
                                                    }
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">{{ $displayStatus
                                                        }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                    $deadline = $schedule->grades->isNotEmpty() ?
                                                    $schedule->grades->first()->validation_deadline : null;
                                                    $formattedDeadline = $deadline ?
                                                    Carbon\Carbon::parse($deadline)->format('d
                                                    M Y') : 'Belum ditentukan';
                                                    @endphp
                                                    {{ $formattedDeadline }}
                                                </td>
                                                <td>
                                                    @if($status === 'dosen_validated')
                                                    <form method="POST"
                                                        action="{{ route('kaprodi.nilai.approve.prodi', $schedule->id) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-warning btn-sm"
                                                            onclick="return confirm('Yakin ingin menyetujui validasi?');">Setujui</button>
                                                    </form>
                                                    @elseif($status === 'kaprodi_approved')
                                                    <form method="POST"
                                                        action="{{ route('staff.nilai.lock', $schedule->id) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Yakin ingin mengunci nilai? Proses ini permanen.');">Kunci</button>
                                                    </form>
                                                    @else
                                                    <span class="badge bg-primary">Nilai Sudah Terkunci</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger">
                                                    Belum ada Nilai Mata Kuliah yangmemerlukan validasi.
                                                </td>
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
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
