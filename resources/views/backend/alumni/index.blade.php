<x-main-layout>
    @section('title', 'Daftar Alumni')

    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Daftar Alumni</h3>
                    <div class="nk-block-des text-soft">
                        <p>Kelola data alumni institusi.</p>
                    </div>
                </div>
                <div class="nk-block-head-actions">
                    <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary">Tambah Alumni</a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Tahun Lulus</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alumni as $index => $alumnus)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $alumnus->student->nim }}</td>
                                <td>{{ $alumnus->student->nama_mhs }}</td>
                                <td>{{ $alumnus->graduation_year }}</td>
                                <td>{{ $alumnus->status }}</td>
                                <td>
                                    <a href="{{ route('admin.alumni.show', $alumnus->id) }}"
                                        class="btn btn-info btn-sm">Detail</a>
                                    <a href="{{ route('admin.alumni.edit', $alumnus->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.alumni.destroy', $alumnus->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin hapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $alumni->links() }}
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
