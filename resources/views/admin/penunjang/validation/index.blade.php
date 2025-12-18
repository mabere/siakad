<x-main-layout>
    @section('title', 'Validasi Kegiatan Penunjang')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Validasi Kegiatan Penunjang</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="row g-3 align-center">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">Filter Status</label>
                            <div class="form-control-wrap">
                                <select class="form-select js-select2" id="status-filter">
                                    <option value="">Semua Status</option>
                                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                                    <option value="approved" @selected(request('status') == 'approved')>Disetujui</option>
                                    <option value="rejected" @selected(request('status') == 'rejected')>Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">Cari</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="search-input" value="{{ request('search') }}" placeholder="Cari judul kegiatan...">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-control-wrap">
                                <button class="btn btn-primary" onclick="applyFilter()">Terapkan Filter</button>
                                <a href="{{ url('admin/penunjang/validation/list') }}" class="btn btn-light">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-bordered mt-3">
            <div class="card-inner">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Dosen</th>
                            <th>Judul Kegiatan</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penunjangs as $penunjang)
                        <tr>
                            <td>{{ $penunjang->date->format('d/m/Y') }}</td>
                            <td>{{ $penunjang->lecturer->nama_dosen }}</td>
                            <td>{{ $penunjang->title }}</td>
                            <td>{{ $penunjang->level }}</td>
                            <td>
                                @if($penunjang->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($penunjang->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.penunjang.validation.show', $penunjang) }}" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $penunjangs->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function applyFilter() {
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search-input').value;
            
            let url = new URL(window.location.href);
            url.searchParams.set('status', status);
            url.searchParams.set('search', search);
            
            window.location.href = url.toString();
        }
    </script>
    @endpush
</x-main-layout>
