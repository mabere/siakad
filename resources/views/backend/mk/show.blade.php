<x-main-layout>
    @section('title', 'Matakuliah Prodi')

    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Mata Kuliah - {{ $department->nama }}</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('admin.mk.create', $department->id) }}" class="btn btn-primary mb-3">
                                    <em class="icon ni ni-plus"></em>
                                    Tambah Mata Kuliah</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row">
                        <div class="col-md-5">
                            <div>
                                <form action="{{ route('admin.course.import') }}" method="POST"
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
                        <div class="col-md-1 ms-1">
                            <a class="btn btn-warning float-end" href="{{ route('admin.course.export') }}">
                                <i class="fa fa-download"></i> Ekspor
                            </a>

                        </div>
                    </div>
                    @if (session('success'))
                    <div class="alert alert-icon alert-success" role="alert">
                        <em class="icon ni ni-check-circle"></em>
                        <strong>{{ session('success') }}</strong>
                    </div>
                    @endif
                    <a href="{{ route('admin.mk.index') }}" class="btn btn-secondary mb-3">Kembali ke Program Studi</a>
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th>
                                </th>
                            </tr>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nama Matkul</th>
                                <th scope="col">SKS</th>
                                <th scope="col">Semester</th>
                                <th scope="col">Program Studi</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $index => $course)
                            <tr>
                                <td>{{ $index + 1 }}.</td>
                                <td>{{ $course->code }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->sks }}</td>
                                <td>{{ $course->semester }}</td>
                                <td>
                                    <a href="{{ route('admin.mk.show', $course->id) }}"
                                        class="btn btn-info btn-sm">Detail</a>
                                    <a href="{{ route('admin.mk.edit', $course->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.mk.destroy', $course->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin hapus?')">Hapus</button>
                                    </form>
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