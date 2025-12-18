<x-main-layout>
    @section('title', 'Data Mahasiswa')

    <div class="container-fluid">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Mahasiswa</h6>
                </div>
                <div class="col-auto d-flex">
                    <a href="{{ route('admin.mhs.dpt.create', $department->id) }}" class="btn btn-success">
                        <i class="icon ni ni-plus"></i> <span class="ms-1">Add Mahasiswa</span>
                    </a>
                    <button type="submit" form="bulkAssignForm" class="btn btn-primary btn-sm d-none"
                        id="bulk-activate-btn" onclick="return confirm('Aktifkan mahasiswa terpilih?')">
                        <i class="icon ni ni-check"></i> <span class="ms-1">Aktifkan</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Form Bulk Assign (Masukkan di luar table) -->
        <form action="{{ route('admin.mhs.assign-multiple') }}" method="POST" id="bulkAssignForm">
            @csrf
        </form>

        <div class="card shadow">
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table nowrap table-hover table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="select-all" form="bulkAssignForm">
                                </th>
                                <th>No.</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Angkatan</th>
                                <th>Program Studi</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mhs as $index => $item)
                            <tr>
                                <td>
                                    @if(!$item->user_id)
                                    <input type="checkbox" form="bulkAssignForm" name="selected_students[]"
                                        value="{{ $item->id }}">
                                    @endif
                                </td>
                                <td>{{ $mhs->firstItem() + $index }}.</td>
                                <td>
                                    {{ $item->nama_mhs }}
                                    @if ($item->user_id)
                                    <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $item->nim }}</td>
                                <td>{{ $item->kelas->angkatan ?? ''}}</td>
                                <td>{{ $item->department->nama }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.mhs.show', $item->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="ico ni ni-eye"></i>
                                        </a>

                                        <!-- Form Edit -->
                                        <a href="{{ route('admin.mhs.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="ico ni ni-edit"></i>
                                        </a>

                                        <!-- Form Delete -->
                                        <form action="{{ route('admin.mhs.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Yakin menghapus data?')">
                                                <i class="icon ni ni-trash"></i>
                                            </button>
                                        </form>

                                        <!-- Form Activate Individual -->
                                        @if($item->user_id === NULL)
                                        <form action="{{ route('admin.assign.mhs', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                title="Aktifkan">
                                                <i class="icon ni ni-user-check"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if ($item->user_id)
                                        <form action="{{ route('admin.mhs.unassign', $item->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning"
                                                onclick="return confirm('Yakin ingin menonaktifkan akun?')">
                                                <em class="icon ni ni-user-remove"></em>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada data mahasiswa
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="m-3">
                        {{ $mhs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox functionality
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('input[name="selected_students[]"][form="bulkAssignForm"]');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
            toggleBulkButton();
        });

        // Toggle bulk activate button visibility
        document.querySelectorAll('input[name="selected_students[]"][form="bulkAssignForm"]').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkButton);
        });

        function toggleBulkButton() {
            const checked = document.querySelectorAll(
                'input[name="selected_students[]"][form="bulkAssignForm"]:checked'
            ).length > 0;

            const bulkBtn = document.getElementById('bulk-activate-btn');
            if (bulkBtn) {
                bulkBtn.classList.toggle('d-none', !checked);
            }
        }
    });
    </script>
    @endpush
</x-main-layout>