<x-main-layout>
    @section('title', 'Manajemen Permintaan Surat')

    <div class="container py-4">
        <div class="alert alert-primary" role="alert">
            <h5 class="mb-1">Manajemen Permintaan Surat</h5>
            <p class="mb-0">Halaman ini menampilkan daftar permintaan surat yang membutuhkan persetujuan dari dekan.
                Anda dapat meninjau detail surat dan mengambil tindakan yang sesuai.</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Permintaan Surat</h5>
                <span class="badge bg-secondary">Total: {{ $ajuanSurat->total() }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Pemohon</th>
                                <th>Judul Surat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ajuanSurat as $key => $surat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $surat->user->name }}</td>
                                <td>{{ $surat->letterType->name }}</td>
                                <td>
                                    @php
                                    $statusClasses = [
                                    'draft' => 'warning',
                                    'submitted' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                    ];
                                    @endphp
                                    <span class="badge bg-{{ $statusClasses[$surat->status] ?? 'secondary' }}">
                                        {{ ucfirst($surat->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('kaprodi.letter-requests.show', $surat->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="ni ni-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada permintaan surat yang tersedia.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $ajuanSurat->links() }}
                </div>
            </div>
        </div>
    </div>
</x-main-layout>