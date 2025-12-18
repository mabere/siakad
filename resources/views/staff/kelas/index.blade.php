<x-main-layout>
    @section('title', 'Data Kelas')

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
                                <a href="{{ route($routePrefix . 'create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Tambah</span>
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
                                <th>No</th>
                                <th>Nama Kelas / ID</th>
                                <th>Angkatan</th>
                                <th>Program Studi</th>
                                @if (auth()->user()->employee?->level === 'faculty')
                                <th>Fakultas</th>
                                @endif

                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->name ?? '-'}} (ID: {{ $item->id ?? '-'}})</td>
                                <td>{{ $item->angkatan }}</td>
                                <td>{{ $item->department->nama }}</td>
                                @if (auth()->user()->employee?->level === 'faculty')
                                <td>{{ $item->department->faculty->nama ?? '-' }}</td>
                                @endif

                                <td>
                                    <form action="{{ route($routePrefix . 'destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route($routePrefix . 'show', $item->id) }}">
                                            <em class="icon ni ni-eye"></em>
                                        </a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route($routePrefix . 'edit', $item->id) }}">
                                            <em class="icon ni ni-edit"></em>
                                        </a>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <em class="icon ni ni-trash-fill"></em>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-warning text-center">
                                    Data tidak tersedia.
                                </td>
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
