<x-main-layout>
    @section('title', 'Data Kegiatan Penunjang')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
            </div>
            <div class="nk-block-head-content d-flex align-items-center">
                <!-- Import Button -->
                <form class="me-1" action="{{ route('lecturer.penunjang.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="file" class="form-control">
                        <button type="submit" class="btn btn-success">
                            <i class="icon ni ni-file me-1"></i> Import
                        </button>
                    </div>
                </form>
                <a class="btn btn-primary" href="{{ route('lecturer.penunjang.export-pdf', request()->query()) }}">
                    <em class="icon ni ni-file-pdf"></em>
                    <span>Unduh</span>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="row mb-3">
                    <div class="col-md-12 justify-content-between d-flex">
                        <form action="{{ route('lecturer.penunjang.index') }}" method="GET" class="form-inline">
                            <div class="row gy-2">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" name="search"
                                                value="{{ request('search') }}" placeholder="Cari judul kegiatan...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <select class="form-select" name="level">
                                                <option value="">Semua Level</option>
                                                @foreach(['Nasional', 'Internasional', 'Regional'] as $level)
                                                <option value="{{ $level }}" {{ request('level')==$level ? 'selected'
                                                    : '' }}>
                                                    {{ $level }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" name="start_date"
                                                value="{{ request('start_date') }}" placeholder="Dari Tanggal">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control" name="end_date"
                                                value="{{ request('end_date') }}" placeholder="Sampai Tanggal">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-search"></em>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Add Data Button -->
                        <a href="{{ route('lecturer.penunjang.create') }}" class="btn btn-primary">
                            <em class="icon ni ni-plus"></em><span>Tambah</span>
                        </a>
                    </div>
                </div>
                <!-- Alert Messages -->
                @if (session('success'))
                <div class="alert alert-success me-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger me-3">{{ session('error') }}</div>
                @endif

                @if(request()->hasAny(['search', 'level', 'start_date', 'end_date']))
                <div class="alert alert-info alert-icon">
                    <em class="icon ni ni-alert-circle"></em>
                    <strong>Filter Aktif</strong>
                    @if(request('search'))
                    <span class="badge bg-primary mx-1">Pencarian: {{ request('search') }}</span>
                    @endif
                    @if(request('level'))
                    <span class="badge bg-primary mx-1">Level: {{ request('level') }}</span>
                    @endif
                    @if(request('start_date'))
                    <span class="badge bg-primary mx-1">Dari: {{ request('start_date') }}</span>
                    @endif
                    @if(request('end_date'))
                    <span class="badge bg-primary mx-1">Sampai: {{ request('end_date') }}</span>
                    @endif
                    <a href="{{ route('lecturer.penunjang.index') }}" class="btn btn-sm btn-outline-danger float-end">
                        Reset Filter
                    </a>
                </div>
                @endif
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Judul</th>
                                    <th>Penyelenggara</th>
                                    <th>Level</th>
                                    <th>Peran</th>
                                    <th>Tanggal</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penunjangs as $penunjang)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $penunjang->title }}</td>
                                    <td>{{ $penunjang->organizer }}</td>
                                    <td>{{ $penunjang->level }}</td>
                                    <td>{{ $penunjang->peran }}</td>
                                    <td>{{ $penunjang->date }}</td>
                                    <td>
                                        @if(filter_var($penunjang->proof, FILTER_VALIDATE_URL))
                                        <a href="{{ $penunjang->proof }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <em class="icon ni ni-link"></em> Lihat URL
                                        </a>
                                        @else
                                        <div class="dropdown">
                                            <a class="dropdown-toggle btn btn-sm btn-outline-primary"
                                                data-bs-toggle="dropdown">
                                                <em class="icon ni ni-file-docs"></em> Dokumen
                                            </a>
                                            <div class="dropdown-menu">
                                                <ul class="link-list-opt">
                                                    <li>
                                                        <a href="/uploads/penunjang/{{ $penunjang->proof }}"
                                                            target="_blank">
                                                            <em class="icon ni ni-eye"></em> <span>Lihat</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="/uploads/penunjang/{{ $penunjang->proof }}" download>
                                                            <em class="icon ni ni-download"></em> <span>Unduh</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($penunjang->status == 'pending')
                                        <span class="badge bg-warning">Menunggu Validasi</span>
                                        @elseif($penunjang->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                        @else
                                        <span class="badge bg-danger" data-bs-toggle="tooltip"
                                            title="{{ $penunjang->rejection_reason }}">
                                            Ditolak
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Show Button -->
                                            <a href="{{ route('lecturer.penunjang.show', $penunjang->id) }}"
                                                class="btn btn-sm btn-sm py-3 px-2 btn-outline-primary" title="Show">
                                                <em class="icon ni ni-eye"></em>
                                            </a>

                                            @if ($penunjang->status === 'approved')
                                            <!-- Approved Indicator -->
                                            <button class="btn btn-sm py-3 px-2 btn-success" disabled
                                                title="Tervalidasi">
                                                <em class="icon ni ni-check"></em>
                                            </button>
                                            @else
                                            <!-- Edit Button -->
                                            <a href="{{ route('lecturer.penunjang.edit', $penunjang->id) }}"
                                                class="btn btn-sm py-3 px-2 btn-outline-warning" title="Edit">
                                                <em class="icon ni ni-edit"></em>
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('lecturer.penunjang.destroy', $penunjang->id) }}"
                                                method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-md btn-icon btn-danger delete-btn"
                                                    title="Hapus">
                                                    <em class="icon ni ni-trash"></em>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $penunjangs->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $(this).closest('form').submit();
            }
        });
    </script>
    @endpush
</x-main-layout>