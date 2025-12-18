<x-main-layout>
    @section('title', 'Manajemen Bimbingan Skripsi')

    <div class="nk-content">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Manajemen Bimbingan Skripsi</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Total {{ $statistics['total'] }} data bimbingan skripsi</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalAssign">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Assign Pembimbing</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="row g-gs">
                        <div class="col-md-4 mb-2">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Total Bimbingan</h6>
                                        </div>
                                    </div>
                                    <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                        <div class="nk-sale-data">
                                            <span class="amount">{{ $statistics['total'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Bimbingan Aktif</h6>
                                        </div>
                                    </div>
                                    <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                        <div class="nk-sale-data">
                                            <span class="amount">{{ $statistics['active'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Bimbingan Selesai</h6>
                                        </div>
                                    </div>
                                    <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                        <div class="nk-sale-data">
                                            <span class="amount">{{ $statistics['completed'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <form action="{{ route('admin.thesis.supervision.index') }}" method="GET" class="row gy-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Program Studi</label>
                                        <select name="department" class="form-select js-select2">
                                            <option value="">Semua Program Studi</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{
                                                request('department')==$department->id ? 'selected' : '' }}>
                                                {{ $department->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Status Bimbingan</label>
                                        <select name="status" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                                                Aktif
                                            </option>
                                            <option value="completed" {{ request('status')=='completed' ? 'selected'
                                                : '' }}>
                                                Selesai
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Cari</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" name="search"
                                                value="{{ request('search') }}"
                                                placeholder="Cari nama/NIM mahasiswa...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <em class="icon ni ni-search"></em>
                                            <span>Filter</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Main Table --}}
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Program Studi</th>
                                        <th>Pembimbing 1</th>
                                        <th>Pembimbing 2</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supervisions as $supervision)
                                    <tr>
                                        <td>
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $supervision->student->nim }} | {{
                                                        $supervision->student->nama_mhs }}</span>
                                                    <span>{{ $supervision->student->nim }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $supervision->student->department->nama }}</td>
                                        <td>
                                            @if($supervision->primarySupervisor)
                                            {{ $supervision->primarySupervisor->nama_dosen }}
                                            @else
                                            <span class="text-warning">Belum ditentukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($supervision->secondarySupervisor)
                                            {{ $supervision->secondarySupervisor->nama_dosen }}
                                            @else
                                            <span class="text-warning">Belum ditentukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $supervision->progress_percentage }}%">
                                                    {{ $supervision->progress_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($supervision->status === 'active')
                                            <span class="badge bg-success">Aktif</span>
                                            @elseif($supervision->status === 'completed')
                                            <span class="badge bg-info">Selesai</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="dropdown-toggle btn btn-icon btn-trigger"
                                                    data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a
                                                                href="{{ url('admin/thesis/supervision/show', $supervision->id) }}">
                                                                <em class="icon ni ni-eye"></em>
                                                                <span>Detail</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a
                                                                href="{{ route('admin.thesis.supervision.edit', $supervision->id) }}">
                                                                <em class="icon ni ni-edit"></em>
                                                                <span>Edit</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form id="delete-form-{{ $supervision->id }}"
                                                                action="{{ route('admin.thesis.supervision.destroy', $supervision->id) }}"
                                                                method="POST" class="d-none">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <a href="#" class="text-danger"
                                                                onclick="event.preventDefault(); confirmDelete({{ $supervision->id }})">
                                                                <em class="icon ni ni-trash"></em>
                                                                <span>Hapus</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data bimbingan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-inner">
                            {{ $supervisions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Assign Pembimbing --}}
    <div class="modal fade" id="modalAssign">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Pembimbing Skripsi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.thesis.supervision.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="student">Mahasiswa</label>
                                    <select class="form-select js-select2" name="student_id" required>
                                        <option value="">Pilih Mahasiswa</option>
                                        @foreach($availableStudents as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->nim }} - {{ $student->nama_mhs }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Pembimbing 1</label>
                                    <select class="form-select js-select2" name="supervisor1_id" required>
                                        <option value="">Pilih Pembimbing 1</option>
                                        @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}">
                                            {{ $lecturer->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Pembimbing 2</label>
                                    <select class="form-select js-select2" name="supervisor2_id" required>
                                        <option value="">Pilih Pembimbing 2</option>
                                        @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}">
                                            {{ $lecturer->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.js-select2').select2({
                dropdownParent: $('#modalAssign')
            });
        });

        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
        // Initialize Select2 for all dropdowns in the modal
        $('#modalAssign .js-select2').select2({
            dropdownParent: $('#modalAssign'),
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });

        // Prevent selecting same supervisor
        $('select[name="supervisor1_id"]').on('change', function() {
            let selected = $(this).val();
            $('select[name="supervisor2_id"] option').prop('disabled', false);
            if (selected) {
                $('select[name="supervisor2_id"] option[value="' + selected + '"]').prop('disabled', true);
            }
            $('select[name="supervisor2_id"]').select2('destroy').select2({
                dropdownParent: $('#modalAssign'),
                width: '100%'
            });
        });

        $('select[name="supervisor2_id"]').on('change', function() {
            let selected = $(this).val();
            $('select[name="supervisor1_id"] option').prop('disabled', false);
            if (selected) {
                $('select[name="supervisor1_id"] option[value="' + selected + '"]').prop('disabled', true);
            }
            $('select[name="supervisor1_id"]').select2('destroy').select2({
                dropdownParent: $('#modalAssign'),
                width: '100%'
            });
        });
    });

    // Delete confirmation
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data bimbingan ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
    </script>
    @endpush
</x-main-layout>