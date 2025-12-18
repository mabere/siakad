<x-main-layout>
    @section('title', 'Halaman Pengajuan Surat')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('student.request.letter.create') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Ajukan Surat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-custom.sweet-alert />

                    @if (request('success'))
                    <div class="alert alert-success">{{ urldecode(request('success')) }}</div>
                    @endif


                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No. Surat</th>
                                            <th>Jenis Surat</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $request->reference_number ?? '-' }}</td>
                                            <td>{{ $request->letterType->name ?? 'Tidak ditemukan' }}</td>
                                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @switch($request->status)
                                                @case('draft')
                                                <span class="badge bg-light text-dark">Draft</span>
                                                @break
                                                @case('submitted')
                                                <span class="badge bg-info">Diajukan</span>
                                                @break
                                                @case('processing')
                                                <span class="badge bg-warning">Diproses</span>
                                                @break
                                                @case('approved')
                                                <span class="badge bg-success">Disetujui</span>
                                                @break
                                                @case('rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                                @break
                                                @default
                                                <span class="badge bg-secondary">{{ $request->status }}</span>
                                                @endswitch
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
                                                                    href="{{ route('student.request.letter.show', $request) }}">
                                                                    <em class="icon ni ni-eye"></em>
                                                                    <span>Detail</span>
                                                                </a>
                                                            </li>
                                                            @if($request->isEditable())
                                                            <li>
                                                                <a
                                                                    href="{{ route('student.request.letter.edit', $request) }}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="cancel-btn"
                                                                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $request->id }}').submit();">
                                                                    <em class="icon ni ni-trash"></em>
                                                                    <span>Batalkan</span>
                                                                </a>
                                                                <form id="delete-form-{{ $request->id }}"
                                                                    action="{{ route('student.request.letter.destroy', $request) }}"
                                                                    method="POST" class="d-none">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </li>
                                                            @endif
                                                            @if($request->document_path)
                                                            <li>
                                                                <a
                                                                    href="{{ route('student.request.letter.download', $request) }}">
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
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada pengajuan surat</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{ $requests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    <script>
        $('.cancel-btn').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url'); // Misalnya: route('student.request.letter.destroy', $letterRequest)

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin membatalkan pengajuan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan saat membatalkan', 'error');
                    }
                });
            }
        });
    });
    </script>
    @endpush
</x-main-layout>
