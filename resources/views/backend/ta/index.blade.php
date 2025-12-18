<x-main-layout>
    @section('title', 'Data Tahun Akademik')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data @yield('title')</h4>
                    <div class="ms-auto">
                        <a href="/admin/ta/create" class="btn btn-sm btn-info ms-auto">Tambah</a>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="zero_config" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tahun Akademik</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->ta }}</td>
                                        <td>{{ $item->semester }}</td>
                                        <td>
                                            @if ($item->status == 0)
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Nonaktif">
                                                <em class="icon ni ni-lock-fill"></em>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-success"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Aktif">
                                                <em class="icon ni ni-unlock-fill"></em>
                                            </button>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-baseline">
                                                @if ($item->status == 0)
                                                <form action="{{ route('admin.academic-years.activate', $item->id) }}"
                                                    method="post" class="d-inline">
                                                    <input type="hidden" value="{{ $item->id }}" name="id">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Aktifkan">
                                                        <em class="icon ni ni-shield-check"></em>
                                                    </button>
                                                </form>
                                                @else
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Sedang Aktif">
                                                    <em class="icon ni ni-unlock-fill"></em>
                                                </button>
                                                @endif
                                                <form action="{{ route('admin.ta.destroy', $item->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a class="btn btn-sm btn-warning m-1"
                                                        href="{{ route('admin.ta.edit', $item->id) }}"><em
                                                            class="icon ni ni-edit"></em></a>
                                                    <button type="submit" class="btn btn-sm btn-danger m-1"><em
                                                            class="icon ni ni-trash"></em>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-main-layout>