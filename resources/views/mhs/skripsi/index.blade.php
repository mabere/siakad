<x-main-layout>
    @section('title', 'Bimbingan Skripsi')

    @push('styles')
    <style>
        .supervision-card {
            height: 100%;
            transition: all 0.3s ease;
        }

        .supervision-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
                                <h3 class="nk-block-title page-title">Bimbingan Skripsi</h3>
                            </div>
                        </div>
                    </div>

                    @if($supervision)
                    <div class="nk-block">
                        <!-- Supervision Status Card -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-group mb-md-0">
                                            <label class="form-label">Status Bimbingan</label>
                                            <div class="form-control-wrap">
                                                @if($supervision->status === 'active')
                                                <span class="badge bg-success">Aktif</span>
                                                @elseif($supervision->status === 'completed')
                                                <span class="badge bg-info">Selesai</span>
                                                @else
                                                <span class="badge bg-danger">Diberhentikan</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supervisors Cards -->
                        <div class="row g-gs mb-3">
                            <div class="col-md-6">
                                <div class="card supervision-card">
                                    <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="card-title mb-0">Pembimbing 1</h5>
                                            <span class="badge bg-primary">
                                                {{ $meetings->where('supervisor_id',
                                                $supervision->supervisor_id)->count() }} Bimbingan
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <strong>{{ $supervision->supervisor->nama_dosen }}</strong><br>
                                            NIDN. {{ $supervision->supervisor->nidn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card supervision-card">
                                    <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="card-title mb-0">Pembimbing 2</h5>
                                            <span class="badge bg-primary">
                                                {{ $meetings->where('supervisor_id',
                                                $supervision->secondarySupervisor->id)->count() }} Bimbingan
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <strong>{{ $supervision->secondarySupervisor->nama_dosen }}</strong><br>
                                            NIDN. {{ $supervision->secondarySupervisor->nidn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Meetings History Card -->
                        <div class="card">
                            <div class="card-inner">
                                <div class="card-head">
                                    <h5 class="card-title">Riwayat Bimbingan</h5>
                                </div>

                                @php
                                $hasPendingMeetingP1 = $meetings->where('supervisor_id', $supervision->supervisor_id)
                                ->where('status', 'pending')
                                ->isNotEmpty();
                                $hasPendingMeetingP2 = $meetings->where('supervisor_id',
                                $supervision->secondarySupervisor->id)
                                ->where('status', 'pending')
                                ->isNotEmpty();
                                @endphp

                                <!-- Warning Alert -->
                                @if($hasPendingMeetingP1 || $hasPendingMeetingP2)
                                <div class="alert alert-pro alert-warning mb-3">
                                    <div class="d-flex">
                                        <em class="icon ni ni-alert-circle fs-4 me-2"></em>
                                        <div>
                                            Anda masih memiliki pengajuan bimbingan yang belum direspon.
                                            Silakan tunggu respon dari dosen pembimbing sebelum mengajukan bimbingan
                                            baru.
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="mb-3">
                                    <a href="{{ route('student.thesis.supervision.meeting.create', 'pembimbing_1') }}"
                                        class="btn btn-info {{ $hasPendingMeetingP1 ? 'disabled' : '' }}"
                                        @if($hasPendingMeetingP1) onclick="return false;" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Anda masih memiliki pengajuan bimbingan yang belum direspon" @endif>
                                        <em class="icon ni ni-plus"></em>
                                        <span>Supervisor 1</span>
                                    </a>
                                    <a href="{{ route('student.thesis.supervision.meeting.create', 'pembimbing_2') }}"
                                        class="btn btn-success {{ $hasPendingMeetingP2 ? 'disabled' : '' }}"
                                        @if($hasPendingMeetingP2) onclick="return false;" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Anda masih memiliki pengajuan bimbingan yang belum direspon" @endif>
                                        <em class="icon ni ni-plus"></em>
                                        <span>Supervisor 2</span>
                                    </a>
                                    <a href="{{ route('student.thesis.print-history') }}" target="_blank"
                                        class="btn btn-warning" data-toggle="tooltip" data-placement="top"
                                        title="Cetak Riwayat Bimbingan">
                                        <i class="icon ni ni-printer"></i>
                                    </a>
                                </div>

                                <!-- Meetings Table -->
                                @if(!empty($meetings) && $meetings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Pembimbing</th>
                                                <th>Topik</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($meetings->sortByDesc('meeting_date') as $meeting)
                                            <tr>
                                                <td>{{ $meeting->meeting_date->format('d/m/Y H:i') }}</td>
                                                <td>{{ $meeting->supervisor->nama_dosen }}</td>
                                                <td>{{ $meeting->topic }}</td>
                                                <td>
                                                    @if($meeting->status === 'pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                    @elseif($meeting->status === 'approved')
                                                    <span class="badge bg-success">Disetujui</span>
                                                    @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('student.thesis.supervision.meeting.show', $meeting->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <em class="icon ni ni-eye"></em>
                                                        <span>Detail</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <em class="icon ni ni-calendar-alt fs-2 text-muted"></em>
                                    <p class="text-muted mt-2">Belum ada riwayat bimbingan.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <em class="icon ni ni-info fs-4 me-2"></em>
                            <p class="mb-0">Anda belum memiliki pembimbing skripsi. Silahkan hubungi program studi untuk
                                penugasan pembimbing.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    @endpush
</x-main-layout>