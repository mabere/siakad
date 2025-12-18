<x-main-layout>
    @section('title', 'Daftar Mata Kuliah')

    <div class="container">
        <h4>@yield('title')</h4>
        <div class="table-responsive">
            <table id="zero_config" class="table table-striped table-bordered">
                <thead class="bg-primary text-white">
                    <tr class="text-center">
                        <th class="p-3">No.</th>
                        <th class="p-3">Mata Kuliah</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Team Teaching</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $kelas)
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align:left">
                            @if ($kelas->schedulable_type === 'App\Models\Course')
                            {{ $kelas->schedulable->name ?? 'N/A' }}
                            @elseif ($kelas->schedulable_type === 'App\Models\MkduCourse')
                            {{ $kelas->schedulable->name ?? 'N/A' }}
                            @else
                            Mata Kuliah Tidak Dikenal
                            @endif
                        </td>
                        <td>Angkatan {{ $kelas->kelas->angkatan ?? 'N/A' }}</td>
                        <td>{{ $kelas->hari ?? 'N/A' }}/Pukul {{ $kelas->start_time->format('H:i') ?? 'N/A' }}</td>
                        <td>
                            @foreach($kelas->lecturersInSchedule as $lecturer)
                            @if($lecturer->id == auth()->user()->lecturer->id)
                            @if(isset($lecturer->pivot->start_pertemuan) && $lecturer->pivot->start_pertemuan <= 8)
                                <span class="badge bg-secondary p-1">Dosen Pertama</span>
                                @else
                                <span class="badge p-1 bg-info">Dosen Kedua</span>
                                @endif
                                @endif
                                @endforeach
                        </td>
                        <td>
                            <a class="btn btn-md btn-outline-warning"
                                href="{{ route('lecturer.bap.show', $kelas->id) }}" data-toggle="tooltip"
                                data-placement="top" title="Isi BAP">
                                <em class="icon ni ni-edit"></em></a>
                            <a class="btn btn-md btn-outline-info"
                                href="{{ route('lecturer.bap.laporan', $kelas->id) }}" data-toggle="tooltip"
                                data-placement="top" title="Cetak Laporan">
                                <em class="icon ni ni-printer-fill"></em></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-warning text-center">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
