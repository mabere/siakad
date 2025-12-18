<x-main-layout>
    @section('title', 'Data Mahasiswa')
    <div class="container py-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Mahasiswa</h5>
                <a href="/admin/mhs/create" class="btn btn-light btn-sm">
                    <i class="ni ni-plus"></i> Tambah Mahasiswa
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 gap-1">
                        <form action="{{ route('admin.mhs.import') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file"></i> Impor
                            </button>
                        </form>
                    </div>
                    <div class="col-md-1 text-end">
                        <a class="btn btn-warning" href="{{ route('admin.mhs.export') }}">
                            <i class="icon ni ni-download"></i> Ekspor
                        </a>
                    </div>
                </div>

                @if (session('success'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="icon ni ni-check-circle me-2"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
                @endif

                <form action="{{ route('admin.mhs.assign-multiple') }}" method="POST">
                    @csrf
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>No.</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Angkatan</th>
                                <th>Program Studi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mhs as $item)
                            <tr>
                                <td>
                                    @if(!$item->user_id)
                                    <input type="checkbox" name="selected_students[]" value="{{ $item->id }}">
                                    @endif
                                </td>
                                <td>{{ ($mhs->currentPage() - 1) * $mhs->perPage() + $loop->iteration }}.</td>
                                <td>
                                    {{ $item->nama_mhs }}
                                    @if ($item->user_id)
                                    <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $item->nim }}</td>
                                <td>{{ $item->kelas->angkatan ?? '-' }}</td>
                                <td>{{ $item->department->nama }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.mhs.show', $item->id) }}">
                                        <em class="icon ni ni-eye"></em>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.mhs.edit', $item->id) }}">
                                        <em class="icon ni ni-edit"></em>
                                    </a>
                                </td>

                                <td>
                                    @if (!$item->user_id)
                                    <!-- Pastikan tombol Aktifkan ada dalam form tersendiri -->
                                    <form action="{{ route('admin.assign.mhs', $item->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <em class="icon ni ni-check"></em> Aktifkan
                                        </button>
                                    </form>
                                    @endif
                                </td>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Data kosong.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary mt-3">Aktifkan Mahasiswa Terpilih</button>
                </form>
                <td>
                    <form action="{{ route('admin.mhs.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <em class="icon ni ni-trash-fill"></em>
                        </button>
                    </form>
                </td>
            </div>
            {{ $mhs->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('select-all').addEventListener('change', function(e) {
            document.querySelectorAll('input[name="selected_students[]"]').forEach(cb => cb.checked = e.target.checked);
        });
    </script>
    @endpush
</x-main-layout>