<x-main-layout>
    @section('title', 'Data Mahasiswa')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Mahasiswa Per Program Studis</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-custom.sweet-alert />

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row">
                        <div class="col-md-5">
                            <div>
                                <form action="{{ route('admin.mhs.import') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file" class="form-control">
                                    <br>

                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-success">
                                <i class="fa fa-file"></i> Impor
                            </button>
                        </div>
                        </form>
                        <div class="col-md-1">
                            <a class="btn btn-warning float-end" href="{{ route('admin.mhs.export') }}">
                                <i class="fa fa-download"></i> Ekspor
                            </a>

                        </div>
                    </div>

                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Program Studi</th>
                                <th class="text-center" scope="col">Fakultas</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($department as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama }} ({{ $item->jenjang }})</td>
                                <td>{{ $item->faculty->nama }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route('admin.mhs.by.department', $item->id) }}"><em
                                            class="icon ni ni-eye"></em></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>No data.</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
