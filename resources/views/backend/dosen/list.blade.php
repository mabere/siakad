<x-main-layout>
    @section('title', 'Data Dosen')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-between g-3">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Daftar Dosen</h3>
                    <div class="nk-block-des text-soft">
                        <p>Prodi: {{ $department->nama }}</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.dosen.index') }}" class="btn p-2 btn-secondary">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Kembali</span>
                        </a>
                        <a href="{{ route('admin.dosen.create') }}" class="btn p-2 btn-sm btn-primary">
                            <em class="icon ni ni-plus"></em>
                            <span>Add</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <!-- Import/Export Section -->
                <div class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-12">
                            <form action="{{ route('admin.lecturer.import') }}" method="POST"
                                enctype="multipart/form-data" class="d-flex gap-2 w-100">
                                @csrf
                                <input type="file" name="file" class="form-control" required>
                                <button class="btn p-3 btn-success">
                                    <i class="icon ni ni-upload"></i> Import
                                </button>
                                <a href="{{ route('admin.lecturer.export', $department->id) }}"
                                    class="btn btn-warning p-3 w-10">
                                    <em class="icon ni ni-download"></em> Export
                                </a>
                            </form>
                        </div>
                    </div>
                </div>

                <x-custom.sweet-alert />

                <!-- Lecturer Table -->
                <div class="table-responsive">
                    <table class="datatable-init nowrap table">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Dosen</th>
                                <th>NIDN</th>
                                <th>Prodi</th>
                                <th width="20%">Status Akun</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dosen as $lecturer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $lecturer->nama_dosen }}</td>
                                <td>{{ $lecturer->nidn }}</td>
                                <td>{{ $lecturer->department->nama }}</td>
                                <td>
                                    @if ($lecturer->user_id != NULL)
                                    <span class="badge bg-success">
                                        <em class="icon ni ni-check-circle"></em> Akun Aktif
                                    </span>
                                    @else
                                    <form action="{{ route('admin.assign.dosen', $lecturer->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Aktifkan Akun
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.dosen.show', $lecturer->id) }}"
                                            class="btn btn-md p-2 btn-info" title="Detail">
                                            <em class="icon ni ni-eye"></em>
                                        </a>
                                        <a href="{{ route('admin.dosen.edit', $lecturer->id) }}"
                                            class="btn btn-md p-2 btn-warning" title="Edit">
                                            <em class="icon ni ni-edit"></em>
                                        </a>
                                        <x-custom.delete-button
                                            :action-url="route('admin.dosen.destroy', $lecturer->id)" class="btn-md" />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
