<x-main-layout>
    @section('title', 'Jadwal Perkuliahan')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Jadwal Mengajar</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('lecturer.edom.index') }}" class="btn btn-primary">
                                    <em class="icon ni ni-reply"></em>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Hari/Waktu</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Kelas</th>
                                <th scope="col">Ruangan</th>
                                <th scope="col">Program Studi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->hari }}/{{ $item->start_time->format('H:i') . ' - ' .
                                    $item->end_time->format('H:i') }}
                                </td>
                                <td>
                                    {{ $item->course_name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">({{ $item->course_code ?? 'N/A' }})</small>
                                </td>
                                <td>{{ $item->kelas->name ?? 'N/A' }}</td>
                                <td>{{ $item->room->name ?? 'N/A' }}</td>
                                <td>
                                    @if($item->schedulable_type === 'App\Models\Course' && $item->course->department)
                                    {{ $item->course->department->nama }}
                                    @else
                                    MKDU / Tidak Berlaku
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-warning">Data jadwal tidak tersedia untuk tahun
                                    akademik ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
