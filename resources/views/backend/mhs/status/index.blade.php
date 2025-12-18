<x-main-layout>
    @section('title', 'Status Mahasiswa')
    <div class="container-fluid">
        <!-- Header & Filter -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Status Mahasiswa - Tahun Akademik {{ $currentAcademicYear->ta }}</h1>
            <form method="GET" action="{{ route('admin.status-mhs.index') }}" class="form-inline">
                <div class="form-group">
                    <label for="academic_year_id" class="mr-2">Tahun Akademik:</label>
                    <input type="number" name="academic_year_id" id="academic_year_id" class="form-control mr-2"
                        value="{{ $currentAcademicYear }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Tabel Data -->
        @if($data->count())
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Mahasiswa</th>
                                <th>Tahun Akademik</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Tanggal Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->student_name }}</td>
                                <td>{{ $item->academic_year }}</td>
                                <td>{{ $item->current_semester }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->latest_update ?
                                    \Carbon\Carbon::parse($item->latest_update)->format('d-m-Y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $data->links() }}
            </div>
        </div>
        @else
        <div class="alert alert-info">
            Tidak ada data status mahasiswa untuk tahun akademik ini.
        </div>
        @endif
    </div>

</x-main-layout>