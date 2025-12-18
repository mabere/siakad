<x-main-layout>
    @section('title', 'Daftar Jadwal Kuliah')

    <div class="container">
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="card-head">
                    <h5 class="card-title">Import Jadwal</h5>
                </div>
                <form action="{{ route('admin.list-jadwal.import', $department->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="importFile">Pilih File Excel/CSV</label>
                        <div class="form-control-wrap">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="importFile" name="file"
                                    accept=".xlsx, .xls, .csv">
                                <label class="custom-file-label" for="importFile">Pilih file</label>
                            </div>
                        </div>
                        @error('file') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Import Jadwal</button>
                        <a href="{{ asset('path/to/your/schedule_template.xlsx') }}" class="btn btn-outline-primary"
                            download>Download Template</a>
                    </div>
                    
                    @if(session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                    @endif
                    @if(session('warning'))
                    <div class="alert alert-warning mt-2">{{ session('warning') }}</div>
                    @if($errors->any())
                    <ul class="text-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                    @endif
                </form>
            </div>
        </div>
        <div class="card">
            <h5 class="card-header bg-warning text-white">@yield('title')</h5>
            <table class="table">
                <tr>
                    <th>Fakultas</th>
                    <td>:</td>
                    <td>{{ $department->faculty->nama }}</td>
                    <th>Tahun Akademik</th>
                    <td>:</td>
                    <td>{{ $ta->ta }}</td>
                </tr>
                <tr>
                    <th>Program Studi</th>
                    <td>:</td>
                    <td>{{ $department->nama }}</td>
                    <th>Semester</th>
                    <td>:</td>
                    <td>{{ $ta->semester }}</td>
                </tr>
                <tr class="mb-3">
                    <th>Mulai Perkuliahan</th>
                    <td>:</td>
                    <td>{{ $ta->start_date }}</td>
                    <th>Akhir Semester</th>
                    <td>:</td>
                    <td>{{ $ta->end_date }}</td>
                </tr>
            </table>
        </div>
        <a class="my-2 btn btn-round btn-primary" href="{{ route('admin.list-jadwal.create', $department->id) }}">Tambah
            Jadwal</a>
        <div class="card">
            <table class="table">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="p-2">No.</th>
                        <th class="p-2">Semester</th>
                        <th class="p-2">Kode/Mata Kuliah</th>
                        <th class="p-2">SKS</th>
                        <th class="p-2">Dosen</th>
                        <th class="p-2">Kelas</th>
                        <th class="p-2">Hari</th>
                        <th class="p-2">Waktu</th>
                        <th class="p-2">Ruang</th>
                        <th class="p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($schedules->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center">No data yet.</td>
                    </tr>
                    @else
                    @foreach($schedules as $key => $schedule)
                    <tr>
                        <td>{{ $key + 1 }}.</td>
                        @php $matkul = $schedule->schedulable; @endphp

                        <td>{{ $matkul->semester_number ?? '-' }}</td>
                        <td>{{ $matkul->code ?? '-' }} - {{ $matkul->name ?? '-' }}</td>
                        <td>{{ $matkul->sks ?? '-' }} sks</td>

                        <td>
                            @forelse($schedule->lecturersInSchedule as $lecturer)
                            {{ $lecturer->nama_dosen }}
                            @if(isset($lecturer->pivot->start_pertemuan) && isset($lecturer->pivot->end_pertemuan))
                            (Minggu {{ $lecturer->pivot->start_pertemuan }}-{{ $lecturer->pivot->end_pertemuan }})
                            @endif
                            @if(!$loop->last)<br>@endif
                            @empty
                            -
                            @endforelse
                        </td>
                        <td>{{ $schedule->kelas->name ?? '-' }}</td>
                        <td>{{ $schedule->hari ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{
                            \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td>{{ $schedule->room->name ?? '-' }} - {{ $schedule->room->nomor ?? '' }}</td>
                        <td width="11%">
                            <form
                                action="{{ route('admin.list-jadwal.delete', [$schedule->department_id, $schedule->id]) }}"
                                method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('admin.list-jadwal.edit', [$schedule->department_id, $schedule->id]) }}"
                                    class="btn btn-warning btn-sm"><i class="icon ni ni-edit"></i></a>
                                <button type="submit" class="btn btn-danger btn-sm"><i
                                        class="icon ni ni-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>
