<x-main-layout>
    @section('title', 'Data Mata Kuliah')
    @php
    $filePath = 'storage/template/Template_Mata_kuliah.xlsx';
    $hashedFile = md5($filePath);
    @endphp

    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Mata Kuliah</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a class="btn btn-outline-primary" href="{{ asset('files/' . $hashedFile) }}"
                                    onclick="event.preventDefault(); window.location.href='{{ asset($filePath) }}';">
                                    <i class="icon ni ni-download me-1"></i> Download Template
                                </a>
                                <a href="{{ route('staff.course.create') }}" class="btn btn-primary">
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
                    <div class="d-flex justify-content-start mb-3">
                        <form action="{{ route('staff.import.course') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex align-items-center">
                            @csrf
                            <input type="file" name="file" class="form-control me-2">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file me-1"></i> Import
                            </button>
                        </form>
                        <a class="ms-1 btn btn-warning" href="{{ url('staff/course/export') }}">
                            <i class="icon ni ni-download me-1"></i> Ekspor
                        </a>
                    </div>
                    <div class="row">
                        <table class="datatable-init nowrap table">
                            <thead>
                                <tr>
                                    <th>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Nama Matkul</th>
                                    <th scope="col">SKS</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courses as $item)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}.</th>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->sks }}</td>
                                    <td>{{ $item->semester_number}}
                                        @if(in_array($item->semester_number, [1, 3, 5, 7]))
                                        (Ganjil)
                                        @elseif(in_array($item->semester_number, [2, 4, 6, 8]))
                                        (Genap)
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('staff.course.destroy', $item->id) }}" method="post"
                                            onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('staff.course.show', $item->id) }}"><em
                                                    class="icon ni ni-eye"></em></a>
                                            <a class="btn btn-sm btn-warning"
                                                href="{{ route('staff.course.edit', $item->id) }}"><em
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
    </div>
    <x-custom.sweet-alert />

</x-main-layout>
