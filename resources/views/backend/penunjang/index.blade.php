<x-main-layout>
    @section('title', 'Kegiatan Penunjang Dosen')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@yield('title')</h4>
                    <div class="ms-auto">
                        <a href="{{ url('admin/penunjang/create') }}" class="btn btn-sm btn-info ms-auto">Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    <x-custom.sweet-alert />
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Dosen</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Peran</th>
                                    <th>Tingkat Kegiatan</th>
                                    <th>Waktu Kegiatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penunjang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->lecturer->nama_dosen ?? 'N/A' }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->peran }}</td>
                                    <td>{{ $item->level }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info" href="{{ url('admin/penunjang', $item->id) }}">
                                            <i class="icon ni ni-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.penunjang.edit', $item->id) }}">
                                            <i class="icon ni ni-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>