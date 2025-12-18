<x-main-layout>
    @section('title', 'Daftar Jadwal Kuliah')

    <div class="container">
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
        <a class="my-2 btn btn-round btn-primary"
            href="{{ route('admin.tambah-jadwal.create', $department->id) }}">Tambah Jadwal</a>
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
                        <td>{{ $schedule->course->semester_number }}</td>
                        <td>{{ $schedule->course->code }} - {{ $schedule->course->name }}</td>
                        <td>{{ $schedule->course->sks }} sks</td>
                        <td>
                            @foreach($schedule->lecturersInSchedule as $lecturer)
                            {{ $lecturer->nama_dosen }}
                            @if(!$loop->last)<br> @endif
                            @endforeach
                        </td>
                        <td>{{ $schedule->kelas->name }}</td>
                        <td>{{ $schedule->hari }}</td>
                        <td>{{ $schedule->waktu }}</td>
                        <td>{{ $schedule->room->name }}-{{ $schedule->room->nomor }}</td>
                        <td width="11%">
                            <form action="{{ route('admin.delete-jadwal.dosen', $schedule->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <a href=" {{ route('admin.edit-jadwal.dosen', $schedule->id) }}"
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
    <x-custom.sweet-alert />
</x-main-layout>
