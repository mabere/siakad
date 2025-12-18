<x-main-layout>
    @section('title', 'Data Publikasi Dosen')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@yield('title')</h4>
                    <div class="ms-auto">
                        <a href="{{ route('lecturer.publication.create') }}"
                            class="btn btn-sm btn-info ms-auto">Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
                    <div class="d-flex justify-content-start mb-3">
                        <form action="{{ route('lecturer.publications.import') }}" method="POST"
                            enctype="multipart/form-data" class="d-flex align-items-center me-1">
                            @csrf
                            <input type="file" name="file" class="form-control me-2">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file"></i> Import
                            </button>
                        </form>
                        <a href="{{ asset('storage/templates/course_import_template.xlsx') }}" class="btn btn-primary">
                                <i class="icon ni ni-xlsx ms-1"></i> Unduh Template
                            </a>
                    </div>

                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Judul</th>
                                    <th>Media</th>
                                    <th>Edisi</th>
                                    <th>Sitasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($publications as $index => $item)
                                <tr>
                                    <td>{{ ($publications->currentPage() - 1) * $publications->perPage() + $index + 1
                                        }}.</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->media }}</td>
                                    <td>{{ $item->issue }}, {{ $item->year }}</td>
                                    <td>{{ $item->citation }}</td>
                                    <td width="175px">

                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('lecturer.publication.show', $item->id) }}"><i
                                                class="icon ni ni-eye"></i></a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('lecturer.publication.edit', $item->id) }}"><i
                                                class="icon ni ni-edit"></i></a>
                                        <x-custom.delete-button
                                            :action-url="route('lecturer.publication.destroy', $item->id)" />
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center p-4" colspan="6">
                                        <span class="alert alert-warning">Anda belum punya data publikasi.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $publications->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>