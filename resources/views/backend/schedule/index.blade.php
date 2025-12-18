<x-main-layout>
    @section('title', 'Daftar Jadwal Perkuliahan')

    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title p-2">Daftar Jadwal Perkuliahan per Prodi</h4>
                    </div>
                </div>
            </div>

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Program Studi</th>
                                <th>Fakultas</th>
                                <th>Jenjang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prodi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->faculty->nama ?? '-' }}</td>
                                <td>{{ $item->jenjang ?? '-' }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route('admin.list-jadwal.show', $item->id) }}">
                                        <em class="icon ni ni-eye"></em>
                                        <span class="d-none d-sm-inline">Lihat Jadwal</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-warning">Data tidak tersedia</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-custom.sweet-alert />
</x-main-layout>
