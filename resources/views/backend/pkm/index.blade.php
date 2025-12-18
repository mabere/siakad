<x-main-layout>
    @section('title', 'Data PKM Dosen')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@yield('title')</h4>
                    <div class="ms-auto">
                        <a href="{{ url('admin/pkm/create') }}" class="btn btn-sm btn-info ms-auto">Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Judul</th>
                                    <th>Pendanaan</th>
                                    <th>Tahun Pelaksanaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->pendanaan }}</td>
                                    <td>{{ $item->year }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info" href="{{ url('admin/pkm', $item->id) }}">
                                            <i class="icon ni ni-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.pkm.edit', $item->id) }}">
                                            <i class="icon ni ni-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td>No data.</td>
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