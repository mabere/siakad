<x-main-layout>
    @section('title', 'Manajemen Kelas Program Studi')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Kelas</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-header bg-primary">
                    <h5 class="card-title text-white">Informasi Akademik</h5>
                </div>
                <div class="card-inner">
                    <table class="nowrap table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Program Studi</th>
                                <th>Fakultas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $dept)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dept->nama }}</td>
                                <td>{{ $dept->faculty->nama }}</td>
                                <td>
                                    <a href="{{ route('admin.kelas.byDepartment', $dept->id) }}"
                                        class="btn btn-primary">Lihat Kelas</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-warning">data tidak tersedia</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-main-layout>