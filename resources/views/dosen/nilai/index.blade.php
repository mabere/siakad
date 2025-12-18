<x-main-layout>
    @section('title', 'Daftar Nilai')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info">
                    <div class="row">
                        <div class="col-5 text-white">
                            <h4>DAFTAR MATA KULIAH</h4>
                        </div>
                        <div class="col-7"></div>
                    </div>
                </div>
                <div class="card-body">
                    <x-custom.sweet-alert />
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <th>No.</th>
                                <th>Hari/Waktu</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Ruangan</th>
                                <th>Program Studi</th>
                                <th>Status Nilai</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $item->hari }}/{{ $item->start_time->format('H:i') . '-' .
                                        $item->end_time->format('H:i') }}</td>
                                    <td>{{ $item->schedulable->name }}</td>
                                    <td>{{ $item->kelas->name }}</td>
                                    <td>{{ $item->room->name }}</td>
                                    <td>{{ $item->department->nama }}</td>
                                    <td>
                                        @php
                                        $status = $item->grades->isNotEmpty() ?
                                        $item->grades->first()->validation_status : 'Belum ada data';
                                        $displayStatus = '';
                                        $statusClass = '';
                                        switch ($status) {
                                        case 'pending':
                                        $displayStatus = 'Dosen Belum Validasi';
                                        $bgClass = 'danger';
                                        break;
                                        case 'dosen_validated':
                                        $displayStatus = 'Divalidasi Dosen';
                                        $bgClass = 'warning';
                                        break;
                                        case 'kaprodi_approved':
                                        $displayStatus = 'Divalidasi Prodi';
                                        $bgClass = 'info';
                                        break;
                                        case 'locked':
                                        $displayStatus = 'Terkunci';
                                        $bgClass = 'success';
                                        break;
                                        default:
                                        $displayStatus = 'Belum ada data';
                                        $bgClass = 'secondary';
                                        }
                                        @endphp
                                        <span class="{{ $statusClass }} badge bg-{{ $bgClass }}">{{ $displayStatus
                                            }}</span>
                                    </td>
                                    <td>
                                        @if ($status === 'locked')
                                        <a class="btn btn-sm btn-secondary"
                                            href="{{ route('lecturer.nilai.print', $item->id) }}"
                                            onclick="return confirm('Yakin ingin mencetak nilai?');"
                                            data-toggle="tooltip" data-placement="top" title="Cetak Nilai"><i
                                                class="icon ni ni-printer"></i></a>
                                        @else
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('lecturer.nilai.show', $item->id) }}" data-toggle="tooltip"
                                            data-placement="top" title="Isi Nilai"><i class="icon ni ni-edit"></i></a>
                                        @endif
                                    </td>

                                </tr>
                                @empty
                                <tr class="text-center">
                                    <td colspan="8" class="text-danger">Data jadwal mengajar belum tersedia.</td>
                                </tr>
                                @endforelse
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
