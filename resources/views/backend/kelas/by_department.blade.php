<x-main-layout>
    @section('title', 'Data Kelas')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title') - {{ $department->nama }}</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('admin.kelas.department.create', $department->id) }}"
                                    class="btn btn-success mb-3">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Add</span>
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
                                <th scope="col">No</th>
                                <th scope="col">Nama Kelas</th>
                                <th scope="col">Angkatan</th>
                                <th scope="col">Program Studi</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas as $item)
                            <tr>
                                <td scope="row">{{ $loop->iteration }}.</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->angkatan }}</td>
                                <td>{{ $item->total }}</td>
                                <td>
                                    <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('admin.kelas.show', $item->id) }}"><em
                                                class="icon ni ni-eye"></em></a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.kelas.edit', $item->id) }}"><em
                                                class="icon ni ni-edit"></em></a>
                                        <button type="submit" onclick="return confirm('Yakin hapus?')"
                                            class="btn btn-sm btn-danger"><em
                                                class="icon ni ni-trash-fill"></em></button>
                                    </form>
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
