<x-main-layout>
    @section('title', 'Data Fakultas')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Mahasiswa</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="/admin/faculty/create" class="btn btn-primary">
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
                    <x-custom.sweet-alert />
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Fakultas</th>
                                <th scope="col">Dekan</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faculty as $item)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}.</th>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->dekan }}</td>
                                <td>
                                    <form action="{{ route('admin.faculty.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('admin.faculty.show', $item->id) }}"><em
                                                class="icon ni ni-eye"></em></a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.faculty.edit', $item->id) }}"><em
                                                class="icon ni ni-edit"></em></a>
                                        <button type="submit" class="btn btn-sm btn-danger"><em
                                                class="icon ni ni-trash-fill"></em></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>No data yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
