<x-main-layout>
    @section('title', 'Daftar Pengajuan Surat Dosen')
    <div class="container">
        <h1>Daftar Pengajuan Surat</h1>
        <a href="{{ route('lecturer.request.surat.create') }}" class="btn btn-primary mb-3">Ajukan Surat Baru</a>
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Surat</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($letterRequests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->letterType->name }}</td>
                    <td>
                        <span
                            class="badge {{ ['submitted' => 'bg-warning', 'processing' => 'bg-info', 'approved' => 'bg-success', 'rejected' => 'bg-danger'][$request->status] ?? 'bg-secondary' }}">
                            {{ $request->status }}
                        </span>
                    </td>
                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('lecturer.request.surat.show', $request) }}"
                            class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada pengajuan surat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-main-layout>