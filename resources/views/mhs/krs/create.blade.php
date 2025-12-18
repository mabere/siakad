<x-main-layout>
    @section('title', 'Daftar Mata Kuliah')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('admin.dosen.create') }}">
                                    <em class="icon ni ni-pluss"></em>
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('student.krs.index') }}" class="btn btn-primary pull-left mb-3">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title ">Silakan pilih Mata Kuliah yang ingin diprogramkan</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Semester</th>
                                            <th>Kode</th>
                                            <th>Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Dosen</th>
                                            <th>Kelas</th>
                                            <th>Hari</th>
                                            <th>Waktu</th>
                                            <th>Ruangan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <form method="post" action="{{ route('student.krs.store') }}">
                                            @csrf
                                            <input type="hidden" name="idMahasiswa" value="{{$idMahasiswa}}">
                                            <input type="hidden" name="idTa" value="{{$idTa}}">
                                            <input type="hidden" name="totalSks" value="{{$totalSks}}">
                                            <input type="hidden" name="maxSks" value="{{$maxSks}}">
                                            @forelse ($data as $index => $item)
                                            <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td width="9%">{{ $item->course->smt }} ({{$item->course->semester}})
                                                </td>
                                                <td>{{ $item->course->code }}</td>
                                                <td>{{ $item->course->name }}</td>
                                                <td>{{ $item->course->sks }}</td>
                                                <td>{{ $item->lecturer ? $item->lecturer->nama_dosen : 'Belum
                                                    ditentukan' }}</td>
                                                <td>{{ $item->kelas->name }}</td>
                                                <td>{{ $item->hari }}</td>
                                                <td>{{ $item->waktu }}</td>
                                                <td>{{ $item->room->name }}</td>
                                                <td>
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $item->id }}" id="defaultCheck{{ $item->id }}"
                                                        name="course[]" data-toggle="tooltip" data-placement="top"
                                                        title="Ambil Mata Kuliah Ini">
                                                </td>
                                            </tr>
                                            </button>
                                            @empty
                                            <tr>
                                                <td>Data tidak tersedia.</td>
                                            </tr>
                                            @endforelse
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-warning float-lg-end m-2" data-toggle="tooltip"
                                    data-placement="top" title="Simpan Mata Kuliah">
                                    <i class="icon ni ni-edit"></i>
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</x-main-layout>