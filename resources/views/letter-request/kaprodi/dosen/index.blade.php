<x-main-layout>
    @section('title', 'Daftar Surat Masuk')
    <div class="container">
        <h1>Daftar Surat Masuk</h1>
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $surat)
                <tr>
                    <td>{{ $surat->id }}</td>
                    <td>{{ $surat->user->name }}</td>
                    <td>{{ $surat->tipeSurat->name }}</td>
                    <td>
                        <span
                            class="badge {{ ['submitted' => 'bg-warning', 'processing' => 'bg-info', 'approved' => 'bg-success', 'rejected' => 'bg-danger'][$surat->status] ?? 'bg-secondary' }}">
                            {{ $surat->status }}
                        </span>
                    </td>
                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('kaprodi.request.review.dosen.show', $surat) }}"
                            class="btn btn-sm btn-primary">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada surat masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $suratMasuk->links() }}
    </div>
</x-main-layout>