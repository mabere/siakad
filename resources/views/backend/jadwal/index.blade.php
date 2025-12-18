<x-main-layout>
    @section('title', 'List Jadwal Perkuliaan')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title p-2">Daftar Jadwal Mengajar</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="/admin/jadwal" class="btn btn-primary">
                                    <em class="icon ni ni-reply"></em>
                                    <span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Program Studi</th>
                                <th scope="col">Fakultas</th>
                                <th scope="col">Jenjang</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prodi as $item)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}.</th>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->faculty->nama }}</td>
                                <td>{{ $item->jenjang }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route('admin.list-jadwal.show', $item->id) }}"><em
                                            class="icon ni ni-eye"></em></a>
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
    <x-custom.sweet-alert />
</x-main-layout>
