<x-main-layout>
    @section('title', 'Manajemen Surat Ajuan Masuk')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Kelola pengajuan dan persetujuan surat.</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('admin.letter-types.index') }}" class="btn btn-primary">
                                        <em class="icon ni ni-setting"></em>
                                        <span>Kelola Tipe Surat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <!-- Tab Navigation -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#pending">
                                            Menunggu Persetujuan
                                            <span class="badge bg-warning">{{ $pendingCount }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#processed">
                                            Sudah Diproses
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Pending Letters Tab -->
                                    <div class="tab-pane active" id="pending">
                                        <table class="datatable-init table">
                                            <thead>
                                                <tr>
                                                    <th>Pemohon</th>
                                                    <th>Jenis Surat</th>
                                                    <th>Tanggal Pengajuan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($requests as $request)
                                                @if($request->status === 'submitted')
                                                <tr>
                                                    <td>{{ $request->user->name }}</td>
                                                    <td>{{ $request->letterType->name ?? 'Tidak ditemukan' }}</td>
                                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge bg-info">Menunggu Persetujuan</span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a class="dropdown-toggle btn btn-icon btn-trigger"
                                                                data-bs-toggle="dropdown">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt">
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('admin.letter-requests.show', $request) }}">
                                                                            <em class="icon ni ni-eye"></em>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#approveModal{{ $request->id }}">
                                                                            <em class="icon ni ni-check-circle"></em>
                                                                            <span>Setujui</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#rejectModal{{ $request->id }}">
                                                                            <em class="icon ni ni-cross-circle"></em>
                                                                            <span>Tolak</span>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        @include('admin.letters.partials.approve-modal', ['request' =>
                                                        $request])

                                                        @include('admin.letters.partials.reject-modal', ['request' =>
                                                        $request])
                                                    </td>
                                                </tr>
                                                @endif
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada pengajuan surat yang
                                                        menunggu persetujuan</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Processed Letters Tab -->
                                    <div class="tab-pane" id="processed">
                                        <table class="datatable-init table">
                                            <thead>
                                                <tr>
                                                    <th>No. Surat</th>
                                                    <th>Pemohon</th>
                                                    <th>Jenis Surat</th>
                                                    <th>Tanggal Selesai</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($requests as $request)
                                                @if(in_array($request->status, ['approved', 'rejected']))
                                                <tr>
                                                    <td>{{ $request->reference_number ?? '-' }}</td>
                                                    <td>{{ $request->user->name }}</td>
                                                    <td>{{ $request->letterType->name ?? 'Tidak ditemukan' }}</td>
                                                    <td>{{ optional($request->completed_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($request->status === 'approved')
                                                        <span class="badge bg-success">Disetujui</span>
                                                        @else
                                                        <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a class="dropdown-toggle btn btn-icon btn-trigger"
                                                                data-bs-toggle="dropdown">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt">
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('admin.letter-requests.show', $request) }}">
                                                                            <em class="icon ni ni-eye"></em>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                    </li>
                                                                    @if($request->document_path)
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('admin.letter-requests.download', $request) }}">
                                                                            <em class="icon ni ni-download"></em>
                                                                            <span>Download</span>
                                                                        </a>
                                                                    </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada pengajuan surat yang
                                                        sudah diproses</td>
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
    </div>
</x-main-layout>