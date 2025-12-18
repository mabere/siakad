<x-main-layout>
    @section('title', 'Dashboard Data Penunjang Dosen')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('admin.penunjang.dashboard.export', request()->query()) }}"
                                    class="btn btn-primary">
                                    <em class="icon ni ni-file-download"></em>
                                    <span>Export Excel</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <form action="{{ route('admin.penunjang.dashboard.detail') }}" method="GET"
                                class="form-inline">
                                <div class="form-group mr-3">
                                    <label class="mr-2">Program Studi:</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Semua Program Studi</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id')==$dept->id ?
                                            'selected' : '' }}>
                                            {{ $dept->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Status:</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Periode:</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="mx-2">-</span>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Overview -->
                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-2">
                                            <div class="card-title">
                                                <h6 class="title">Total Kegiatan</h6>
                                            </div>
                                        </div>
                                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                            <div class="nk-sale-data">
                                                <span class="amount">{{ $statistics['total'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-2">
                                            <div class="card-title">
                                                <h6 class="title">Approved</h6>
                                            </div>
                                        </div>
                                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                            <div class="nk-sale-data">
                                                <span class="amount text-success">{{ $statistics['approved'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-2">
                                            <div class="card-title">
                                                <h6 class="title">Pending</h6>
                                            </div>
                                        </div>
                                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                            <div class="nk-sale-data">
                                                <span class="amount text-warning">{{ $statistics['pending'] }}
                                                    @if ($statistics['pending'] < 1) @else <a style="margin-left:9rem"
                                                        class="p-1 badge-rounded badge bg-warning text-white btn-secondary"
                                                        href="{{ url('admin/penunjang/validation/list') }}"
                                                        data-toggle="tooltip" data-placement="top" title="Periksa Data">
                                                        <i class="icon ni ni-edit"></i></a>
                                                        @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-2">
                                            <div class="card-title">
                                                <h6 class="title">Rejected</h6>
                                            </div>
                                        </div>
                                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                            <div class="nk-sale-data">
                                                <span class="amount text-danger">{{ $statistics['rejected'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Department Statistics -->
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h5 class="title">Statistik per Program Studi</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Program Studi</th>
                                                <th>Total</th>
                                                <th>Approved</th>
                                                <th>Pending</th>
                                                <th>Rejected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($departmentStats as $dept => $stats)
                                            <tr>
                                                <td>{{ $dept }}</td>
                                                <td>{{ $stats['total'] }}</td>
                                                <td class="text-success">{{ $stats['approved'] }}</td>
                                                <td class="text-warning">{{ $stats['pending'] }}</td>
                                                <td class="text-danger">{{ $stats['rejected'] }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data statistik department
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h5 class="title">Daftar Kegiatan Terbaru</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Dosen</th>
                                                <th>Program Studi</th>
                                                <th>Judul Kegiatan</th>
                                                <th>Level</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($penunjangs as $penunjang)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($penunjang->date)->format('d/m/Y') }}</td>
                                                <td>{{ $penunjang->lecturer->nama_dosen }}</td>
                                                <td>{{ optional($penunjang->lecturer->department)->nama }}</td>
                                                <td>{{ $penunjang->title }}</td>
                                                <td>{{ $penunjang->level }}</td>
                                                <td>
                                                    @switch($penunjang->status)
                                                    @case('approved')
                                                    <span class="badge bg-success text-white">Approved</span>
                                                    @break
                                                    @case('rejected')
                                                    <span class="badge bg-danger text-white">Rejected</span>
                                                    @break
                                                    @default
                                                    <span class="badge bg-warning text-white">Pending</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.penunjang.validation.show', $penunjang->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <em class="icon ni ni-eye"></em>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data kegiatan</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
