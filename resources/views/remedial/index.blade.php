<x-main-layout>

    @section('title', 'Pengajuan Perbaikan Nilai')
    <style>
        .table-container {
            max-width: 800px;
            margin: 50px auto;
        }
    </style>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Pengajuan Berlangsung -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-list-fill me-2"></i>Ajuan Remedial Berlangsung</h5>
        </div>
        <div class="card-body">
            @if ($ongoingRequests->isEmpty())
            <p>Tidak ada permohonan ajuan Remedial nilai perkuliahan.</p>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ongoingRequests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->course->name ?? 'Mata Kuliah Tidak Ditemukan' }}</td>
                        <td>{{ $request->semester }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $request->status)) }}</td>
                        <td>
                            @if (auth()->user()->hasRole('mahasiswa'))
                            <a href="{{ route('mhs.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('dosen'))
                            <a href="{{ route('dosen.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('staff'))
                            <a href="{{ route('staff.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('kaprodi'))
                            <a href="{{ route('kaprodi.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <!-- Riwayat Pengajuan -->
    <div class="card">
        <div class="card-header bg-info text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-history me-2"></i>Riwayat Pengajuan Remedial</h5>
        </div>
        <div class="card-body">
            @if ($historyRequests->isEmpty())
            <p>Tidak ada riwayat pengajuan.</p>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historyRequests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->course->name ?? 'Mata Kuliah Tidak Ditemukan' }}</td>
                        <td>{{ $request->semester }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $request->status)) }}</td>
                        <td>
                            @if (auth()->user()->hasRole('mahasiswa'))
                            <a href="{{ route('mhs.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('kaprodi'))
                            <a href="{{ route('kaprodi.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('dosen'))
                            <a href="{{ route('dosen.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @elseif (auth()->user()->hasRole('staff'))
                            <a href="{{ route('staff.remedial.show', $request) }}" class="btn btn-sm btn-info">Lihat
                                Detail</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <!-- Aksi Tambah (khusus Mahasiswa) -->
    @can('create-remedial')
    <div class="mt-3">
        <a href="{{ route('mhs.remedial.create') }}" class="btn btn-primary px-4">
            <i class="icon ni ni-plus me-2"></i>Ajukan Perbaikan Nilai
        </a>
    </div>
    @endcan
    <x-custom.sweet-alert />
</x-main-layout>
