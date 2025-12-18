<x-main-layout>
    @section('title', 'Manajemen Pegawai')

    <div class="container py-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Tenaga Kependidikan</h5>
                <a href="{{ route('admin.pegawai.create') }}" class="btn btn-light btn-sm">
                    <i class="ni ni-plus"></i> Tambah Data
                </a>
            </div>
            <div class="card-body">
                @if(@session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <table class="datatable-init nowrap table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Program Studi</th>
                                    <th>Posisi</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pegawai as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>
                                        @if ($item->department_id)
                                        {{ $item->department->nama }}
                                        @else
                                        KTU
                                        @endif
                                    </td>
                                    <td>{{ $item->position }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <form action="{{ route('admin.pegawai.destroy', $item->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('admin.pegawai.show', $item->id) }}"><em
                                                    class="icon ni ni-eye"></em></a>
                                            <a class="btn btn-sm btn-warning"
                                                href="{{ route('admin.pegawai.edit', $item->id) }}"><em
                                                    class="icon ni ni-edit"></em></a>
                                            <button type="submit" class="btn btn-sm btn-danger"><em
                                                    class="icon ni ni-trash-fill"></em></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pegawai.</td>
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