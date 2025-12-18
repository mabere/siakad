<x-main-layout>
    @section('title', 'Halaman Manajemen Mata Kuliah Program Studi')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">

                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-header bg-secondary">
                    <h4 class="nk-block-title text-white">@yield('title')</h4>
                </div>
                <div class="card-inner">
                    <table class="nowrap table">
                        <!-- class="datatable-init" -->
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Program Studi</th>
                                <th scope="col">Fakultas</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $index => $department)
                            <tr>
                                <td>{{ $index + 1 }}.</td>
                                <td>{{ $department->nama }}</td>
                                <td>{{ $department->faculty->nama }}</td>
                                <td>
                                    <a href="{{ route('admin.mk.byDepartment', $department->id) }}"
                                        class="btn btn-primary">Lihat
                                        Mata Kuliah</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>