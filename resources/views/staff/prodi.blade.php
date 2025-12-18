<x-main-layout>
    @section('title', 'Program Studi Saya')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Program Studi Saya</h3>
                <div class="nk-block-des text-soft">
                    <p>Program studi yang Anda kelola sebagai staff.</p>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
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
                                <td>1</td>
                                <td>{{ $department->nama }}</td>
                                <td>{{ $department->jenjang }}</td>
                                <td>
                                    {{-- Tentukan prefix route berdasarkan level pengguna --}}
                                    @php
                                    $routePrefix = Auth::user()->employee->level === 'faculty' ? 'ktu' : 'staff';
                                    @endphp
                                    <a href="{{ route($routePrefix . '.department.show', $department->id) }}"
                                        class="btn btn-info btn-sm">
                                        Detail Prodi
                                    </a>
                                    <a href="{{ route($routePrefix . '.mahasiswa.index') }}"
                                        class="btn btn-info btn-sm">
                                        Lihat Mahasiswa
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Anda tidak memiliki program studi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
