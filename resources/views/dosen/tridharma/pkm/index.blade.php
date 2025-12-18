<x-main-layout>
    @section('title', 'Data PKM Dosen')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@yield('title')</h4>
                    <div class="ms-auto">
                        <a href="{{ route('lecturer.pkm.create') }}" class="btn btn-sm btn-info ms-auto">Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="d-flex justify-content-start mb-3">
                        <form action="{{ route('lecturer.pkm.import') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex align-items-center">
                            @csrf
                            <input type="file" name="file" class="form-control me-2">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file me-1"></i> Import
                            </button>
                        </form>
                    </div>
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
                                    <td width="175px">
                                        <form action="{{ route('lecturer.pkm.destroy', $item->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('lecturer.pkm.show', $item->id) }}"><i
                                                    class="icon ni ni-eye"></i></a>
                                            <a class="btn btn-sm btn-warning"
                                                href="{{ route('lecturer.pkm.edit', $item->id) }}"><i
                                                    class="icon ni ni-edit"></i></a>
                                            <button type="submit" class="btn btn-sm btn-danger"><i
                                                    class="icon ni ni-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center p-4 text-warning" colspan="6">
                                        <span class="bg-waring">Anda belum punya data Pengabdian dosen.</span>
                                    </td>
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