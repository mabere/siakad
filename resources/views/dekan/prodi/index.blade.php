<x-main-layout>
    @section('title', 'Daftar Program Studi')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Daftar Program Studi</h3>
                <div class="nk-block-des text-soft">
                    <p>Daftar program studi di fakultas Anda.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-bordered card-stretch">
        <div class="card-inner-group">
            <div class="card-inner p-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Program Studi</th>
                                <th>Jenjang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $department)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $department->nama }}</td>
                                <td>{{ $department->jenjang }}</td>
                                <td>
                                    <a href="{{ route('dekan.departments.show', $department->id) }}"
                                        class="btn btn-info btn-sm">Detail Prodi</a>
                                    <a href="{{ route('dekan.department.student-details', $department->id) }}"
                                        class="btn btn-info btn-sm">Lihat Mahasiswa</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada program studi di fakultas ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>