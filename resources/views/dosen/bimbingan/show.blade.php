<x-main-layout>
    @section('title', 'Detail Bimbingan Skripsi')

    @push('styles')
    <style>
        .meeting-card {
            transition: all 0.3s ease;
        }

        .meeting-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            padding-bottom: 20px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dbdfea;
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: -5px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6576ff;
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
                                <h3 class="nk-block-title page-title">Detail Bimbingan Skripsi</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('lecturer.thesis.supervision.index') }}"
                                    class="btn btn-outline-secondary">
                                    <em class="icon ni ni-arrow-left"></em>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <!-- Student Information Card -->
                        <div class="card mb-3">
                            <div class="card-inner">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="card bg-lighter">
                                            <div class="card-inner">
                                                <h5 class="card-title mb-3">Informasi Mahasiswa</h5>
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">Nama Mahasiswa</span>
                                                        <span class="lead-text">{{
                                                            $supervision->thesis->student->nama_mhs }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">NIM</span>
                                                        <span class="lead-text">{{ $supervision->thesis->student->nim
                                                            }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">Program Studi</span>
                                                        <span class="lead-text">{{
                                                            $supervision->thesis->student->department->nama }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">Status Bimbingan</span>
                                                        <span
                                                            class="badge bg-{{ $supervision->status === 'active' ? 'success' : ($supervision->status === 'completed' ? 'primary' : 'danger') }}">
                                                            {{ $supervision->status === 'active' ? 'Aktif' :
                                                            ($supervision->status === 'completed' ? 'Selesai' :
                                                            'Tidak Aktif')
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card bg-lighter">
                                            <div class="card-inner">
                                                <h5 class="card-title mb-3">Informasi Skripsi</h5>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <span class="sub-text">Judul Skripsi</span>
                                                        <span class="lead-text">{{ $supervision->thesis->title ?? 'Belum
                                                            ada judul' }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">Tanggal Mulai</span>
                                                        <span class="lead-text">{{ $supervision->thesis->start_date ?
                                                            $supervision->thesis->start_date->format('d/m/Y') : '-'
                                                            }}</span>
                                                    </div>
                                                    @if($supervision->thesis->end_date)
                                                    <div class="col-sm-6">
                                                        <span class="sub-text">Tanggal Selesai</span>
                                                        <span class="lead-text">{{
                                                            $supervision->thesis->end_date->format('d/m/Y') }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-2">
                                    @if($supervision->thesis->supervisions->count() > 1)
                                    <h5>Pembimbing:</h5>
                                    1. {{ $supervision->supervisor->nama_dosen }} (Pembimbing
                                    @if ($supervision->supervisor_role == 'pembimbing_1')
                                    1
                                    @else
                                    2
                                    @endif)

                                    @foreach($supervision->thesis->supervisions as $otherSupervision)
                                    @if($otherSupervision->supervisor_id != auth()->user()->lecturer->id)
                                    <br>2. {{ $otherSupervision->supervisor->nama_dosen }}
                                    (Pembimbing @if ($otherSupervision->supervisor_role == 'pembimbing_1')
                                    1
                                    @else
                                    2
                                    @endif)
                                    @endif
                                    @endforeach
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <em class="icon ni ni-info"></em>
                                    <strong>Info:</strong>
                                    Anda adalah Pembimbing @if ($supervision->supervisor_role == 'pembimbing_1')
                                    1
                                    @else
                                    2
                                    @endif untuk mahasiswa ini.
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Supervision Meetings -->
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-4">Riwayat Bimbingan</h5>

                                <!-- Pending Meetings -->
                                @php
                                $pendingMeetings = $meetings->where('status', 'pending');
                                @endphp

                                @if($pendingMeetings->isNotEmpty())
                                <div class="nk-block mb-4">
                                    <h6 class="overline-title text-warning mb-3">Menunggu Tanggapan</h6>
                                    @foreach($pendingMeetings as $meeting)
                                    <div class="card meeting-card bg-lighter mb-3">
                                        <div class="card-inner">
                                            <div class="row g-4">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">{{ $meeting->topic }}</h6>
                                                    <p class="small text-muted mb-2">
                                                        Diajukan untuk: {{ $meeting->meeting_date->format('d/m/Y H:i')
                                                        }}
                                                    </p>
                                                    <p class="mb-0">{{ $meeting->description }}</p>
                                                    @if($meeting->attachment_path)
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($meeting->attachment_path) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <em class="icon ni ni-download"></em>
                                                            <span>Download Lampiran</span>
                                                        </a>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-md-end">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#respondModal-{{ $meeting->id }}">
                                                        <em class="icon ni ni-comments"></em>
                                                        <span>Tanggapi</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                </div>
                                @endif

                                <!-- Meeting History Timeline -->
                                <div class="nk-block">
                                    <h6 class="overline-title text-primary mb-3">Riwayat Bimbingan</h6>
                                    @php
                                    $historyMeetings = $meetings->where('status', '!=',
                                    'pending')->sortByDesc('meeting_date');
                                    @endphp

                                    @forelse($historyMeetings as $meeting)
                                    <div class="timeline-item">
                                        <div class="timeline-content meeting-card">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span
                                                    class="badge bg-{{ $meeting->status === 'approved' ? 'success' : 'danger' }}">
                                                    {{ $meeting->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                                </span>
                                                <small class="text-muted">{{ $meeting->meeting_date->format('d/m/Y H:i')
                                                    }}</small>
                                            </div>
                                            <h6 class="mb-2">{{ $meeting->topic }}</h6>
                                            <p class="mb-2">{{ $meeting->description }}</p>
                                            @if($meeting->attachment_path)
                                            <div class="mb-2">
                                                <a href="{{ Storage::url($meeting->attachment_path) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <em class="icon ni ni-download"></em>
                                                    <span>Download Lampiran</span>
                                                </a>
                                            </div>
                                            @endif
                                            <div class="bg-lighter p-3 rounded">
                                                <p class="mb-1"><strong>Tanggapan:</strong></p>
                                                <p class="mb-0">{{ $meeting->notes }}</p>
                                                <small class="text-muted">
                                                    Ditanggapi pada: {{ $meeting->response_date->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-4">
                                        <em class="icon ni ni-calendar-alt fs-2 text-muted"></em>
                                        <p class="text-muted mt-2">Belum ada riwayat bimbingan.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
