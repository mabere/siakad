@php
use App\Models\SupervisionMeeting;
@endphp

<x-main-layout>
    @section('title', 'Pembimbingan Skripsi')

    @push('styles')
    <style>
        .supervision-card {
            height: 100%;
            transition: all 0.3s ease;
        }

        .supervision-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    @endpush

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Pembimbingan Skripsi</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Meetings Section -->
                    @if($pendingMeetings->isNotEmpty())
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-4">Pengajuan Bimbingan yang Perlu Ditanggapi</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Mahasiswa</th>
                                                <th>NIM</th>
                                                <th>Topik</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingMeetings as $meeting)
                                            <tr>
                                                <td>{{ $meeting->meeting_date->format('d/m/Y H:i') }}</td>
                                                <td>{{ $meeting->thesis->student->nama_mhs }}</td>
                                                <td>{{ $meeting->thesis->student->nim }}</td>
                                                <td>{{ $meeting->topic }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#respondModal-{{ $meeting->id }}">
                                                        <em class="icon ni ni-comments"></em>
                                                        <span>Tanggapi</span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Response Modal -->
                                            <div class="modal fade" id="respondModal-{{ $meeting->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tanggapi Bimbingan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('lecturer.thesis.supervision.meeting.respond', $meeting->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="approved">Setuju</option>
                                                                        <option value="rejected">Tolak</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="form-label">Catatan</label>
                                                                    <textarea name="notes" class="form-control" rows="4"
                                                                        required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan
                                                                    Tanggapan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Supervised Students Section -->
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-4">Daftar Mahasiswa Bimbingan</h5>
                                <div class="row g-gs">
                                    @forelse($supervisions as $thesis_id => $thesis_supervisions)
                                    @php
                                    $supervision = $thesis_supervisions->first();
                                    $student = $supervision->thesis->student;
                                    @endphp
                                    <div class="col-md-6 col-xxl-4">
                                        <div class="card supervision-card">
                                            <div class="card-inner">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="card-title mb-0">{{ $student->nama_mhs }}</h6>
                                                    <span class="badge bg-primary">{{ $supervision->meeting_count }}
                                                        Bimbingan</span>
                                                </div>
                                                <p class="mb-1">NIM: {{ $student->nim }}</p>
                                                <p class="mb-3">Program Studi: {{ $student->department->nama }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="badge bg-{{ $supervision->status === 'active' ? 'success' : ($supervision->status === 'completed' ? 'primary' : 'danger') }}">
                                                        {{ $supervision->status === 'active' ? 'Aktif' :
                                                        ($supervision->status === 'completed' ? 'Selesai' :
                                                        'Tidak Aktif')
                                                        }}
                                                    </span>
                                                    <a href="{{ route('lecturer.thesis.supervision.show', $supervision->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <em class="icon ni ni-eye"></em>
                                                        <span>Detail</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="text-center py-4">
                                            <em class="icon ni ni-users fs-2 text-muted"></em>
                                            <p class="text-muted mt-2">Belum ada mahasiswa bimbingan.</p>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Meetings Section -->
                    @if($recentMeetings->isNotEmpty())
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-4">Riwayat Bimbingan Terakhir</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Mahasiswa</th>
                                                <th>Topik</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentMeetings as $meeting)
                                            <tr>
                                                <td>{{ $meeting->meeting_date->format('d/m/Y H:i') }}</td>
                                                <td>{{ $meeting->thesis->student->nama_mhs }}</td>
                                                <td>{{ $meeting->topic }}</td>
                                                <td>
                                                    @if($meeting->status === 'approved')
                                                    <span class="badge bg-success">Disetujui</span>
                                                    @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('lecturer.thesis.supervision.show', $meeting->thesis->supervisions->first()->id) }}">
                                                        Lihat Bimbingan
                                                    </a>

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
