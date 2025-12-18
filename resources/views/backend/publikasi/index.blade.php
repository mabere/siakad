<x-main-layout>
    @section('title', 'Data Publikasi')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-block">
                    <div class="card card-bordered shadow-sm">
                        <!-- Header -->
                        <div class="card-header py-2"
                            style="background: linear-gradient(90deg, #007bff 0%, #0056b3 100%)">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-white mb-0">@yield('title')</h5>
                                <div class="card-tools">
                                    <a href="{{ url('admin/riset/create') }}" class="btn btn-sm btn-info">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Tambah</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Alert Sukses -->
                            @if (session('success'))
                            <div class="alert alert-fill alert-success alert-dismissible" role="alert">
                                <em class="icon ni ni-check-circle"></em>
                                {{ session('success') }}
                                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </button>
                            </div>
                            @endif

                            <!-- Tabel -->
                            <div class="table-responsive">
                                <table id="zero_config" class="table table-hover table-bordered align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">No.</th>
                                            <th>Judul</th>
                                            <th>Media</th>
                                            <th>Edisi</th>
                                            <th>Sitasi</th>
                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($publications as $item)
                                        <tr>
                                            <td class="text-center">{{ $publications->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->media }}</td>
                                            <td>{{ $item->issue }}, {{ $item->year }}</td>
                                            <td>{{ $item->citation }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ url('admin/publication', $item->id) }}"
                                                        class="btn btn-icon btn-info" title="Lihat">
                                                        <em class="icon ni ni-eye"></em>
                                                    </a>
                                                    <a href="{{ route('admin.publication.edit', $item->id) }}"
                                                        class="btn btn-icon btn-warning" title="Edit">
                                                        <em class="icon ni ni-edit"></em>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <em class="icon ni ni-alert-circle"></em> Tidak ada data.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginasi -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Menampilkan {{ $publications->firstItem() }} - {{ $publications->lastItem() }} dari
                                    {{ $publications->total() }} data
                                </div>
                                <div>
                                    {{ $publications->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .nk-content {
            padding: 1.5rem 0;
        }

        .card-bordered {
            border: 1px solid #e5e9f2;
            border-radius: 6px;
        }

        .card-header {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .thead-light {
            background: #f5f6fa;
            color: #526484;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .btn-icon {
            padding: 0.4rem 0.6rem;
        }

        .alert-fill {
            background: #e8f9f4;
            border: none;
            color: #1ee0ac;
        }

        .table-hover tbody tr:hover {
            background: #f8f9fd;
        }
    </style>
    @endpush
</x-main-layout>