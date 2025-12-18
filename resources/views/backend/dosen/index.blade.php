<x-main-layout>
    @section('title', 'Data Dosen')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Dosen Program Studi</h4>
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
                                <form action="{{ route('admin.lecturer.import') }}" method="POST"
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


                    </div>
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Program Studi</th>
                                <th class="text-center" scope="col">Jenjang</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dosen as $lable => $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama }}</td>
                                <td class="text-center">{{ $item->jenjang }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route('admin.dosen.by.department', $item->id) }}"><em
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
