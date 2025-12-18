<x-main-layout>
    @section('title', 'Detail Bimbingan Skripsi')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><em class="icon ni ni-check-circle"></em></strong>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><em class="icon ni ni-cross-circle"></em></strong>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Detail Bimbingan Skripsi</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('student.thesis.supervision.index') }}"
                                        class="btn btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-gs">
                                    <!-- Thesis Information -->
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Informasi Skripsi</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1">Status</p>
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
                                                    <div class="col-md-6">
                                                        <p class="mb-1">Tanggal Mulai</p>
                                                        <p class="text-dark">{{
                                                            $supervision->thesis->start_date->format('d/m/Y') }}</p>
                                                    </div>
                                                    @if($supervision->thesis->title)
                                                    <div class="col-12">
                                                        <p class="mb-1">Judul Skripsi</p>
                                                        <p class="text-dark">{{ $supervision->thesis->title }}</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supervisors Information -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Pembimbing 1</h5>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span class="tb-lead">{{ $supervision->supervisor->nama_dosen
                                                            }}</span>
                                                        <span>{{ $supervision->supervisor->nidn }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Pembimbing 2</h5>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span class="tb-lead">{{
                                                            $supervision->secondarySupervisor->nama_dosen }}</span>
                                                        <span>{{ $supervision->secondarySupervisor->nidn }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supervision History -->
                                    @if(!empty($meetings))
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h5 class="card-title">Riwayat Bimbingan</h5>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Pembimbing</th>
                                                            <th>Catatan</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($meetings as $meeting)
                                                        <tr>
                                                            <td>{{ $meeting->meeting_date->format('d/m/Y') }}</td>
                                                            <td>{{ $meeting->supervisor->nama_dosen }}</td>
                                                            <td>{{ $meeting->notes }}</td>
                                                            <td>
                                                                @if($meeting->status === 'approved')
                                                                <span class="badge bg-success">Disetujui</span>
                                                                @elseif($meeting->status === 'rejected')
                                                                <span class="badge bg-danger">Ditolak</span>
                                                                @else
                                                                <span class="badge bg-warning">Menunggu</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const disabledButtons = document.querySelectorAll('.btn.disabled[title]');
    
    disabledButtons.forEach(button => {
        // Initialize Bootstrap tooltip
        new bootstrap.Tooltip(button);
        
        // Add click handler for better user feedback
        button.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Tidak dapat mengajukan bimbingan',
                text: 'Anda masih memiliki pengajuan bimbingan yang belum direspon. Silakan tunggu respon dari dosen pembimbing.',
                confirmButtonText: 'Mengerti'
            });
        });
    });
});
</script>
@endpush