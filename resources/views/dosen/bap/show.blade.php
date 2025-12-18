<x-main-layout>
    @section('title', 'Pengisian Berita Acara Perkuliahan (BAP)')

    <div class="container">
        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <h4 class="card-title">@yield('title')</h4>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Mata Kuliah</th>
                            <td>:</td>
                            <td>
                                @if ($jadwal->schedulable_type === 'App\Models\Course')
                                {{ $jadwal->schedulable->name ?? 'N/A' }}
                                @elseif ($jadwal->schedulable_type === 'App\Models\MkduCourse')
                                {{ $jadwal->schedulable->name ?? 'N/A' }}
                                @else
                                Mata Kuliah Tidak Dikenal
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>:</td>
                            <td>
                                @if ($jadwal->schedulable_type === 'App\Models\Course' &&
                                $jadwal->schedulable->department)
                                {{ $jadwal->schedulable->department->nama ?? 'N/A' }}
                                @else
                                Mata Kuliah Dasar Umum
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>:</td>
                            <td>{{ $jadwal->kelas->name }}</td>
                        </tr>
                        <tr>
                            <th>SKS</th>
                            <td>:</td>
                            <td>
                                @if ($jadwal->schedulable_type === 'App\Models\Course')
                                {{ $jadwal->schedulable->sks ?? 'N/A' }} SKS
                                @elseif ($jadwal->schedulable_type === 'App\Models\MkduCourse')
                                {{ $jadwal->schedulable->sks ?? 'N/A' }} SKS
                                @else
                                N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>:</td>
                            <td>
                                @if ($jadwal->schedulable_type === 'App\Models\Course')
                                {{ $jadwal->schedulable->semester_number ?? 'N/A' }}
                                @elseif ($jadwal->schedulable_type === 'App\Models\MkduCourse')
                                {{ $jadwal->schedulable->semester_number ?? 'N/A' }}
                                @else
                                N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <table id="zero_config" class="table table-striped table-bordered">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="p-3">No.</th>
                    <th class="p-3">Dosen Pengampu</th>
                    <th class="p-3">Pertemuan</th>
                    <th class="p-3">Hari/Waktu</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                $authLecturerId = auth()->user()->lecturer->id;
                @endphp
                @for ($i = 1; $i <= 16; $i++) <tr>
                    <td class="text-center">{{ $i }}.</td>
                    <td>
                        @php
                        $foundLecturerForMeeting = false;
                        $currentLecturer = null;
                        @endphp
                        @if ($jadwal->lecturersInSchedule->isNotEmpty())
                        @foreach ($jadwal->lecturersInSchedule as $lecturerItem)
                        @if ($i >= $lecturerItem->pivot->start_pertemuan && $i <= $lecturerItem->pivot->end_pertemuan)
                            {{ $lecturerItem->nama_dosen ?? 'N/A' }}
                            @php
                            $foundLecturerForMeeting = true;
                            $currentLecturer = $lecturerItem;
                            @endphp
                            @break
                            @endif
                            @endforeach
                            @endif
                            @if (!$foundLecturerForMeeting)
                            <p>Tidak ada dosen terhubung</p>
                            @endif
                    </td>
                    <td>Pertemuan ke-{{ $i }}</td>
                    <td>{{ $jadwal->hari ?? 'N/A' }}/Pukul {{ $jadwal->start_time->format('H:i') . '-' .
                        $jadwal->end_time->format('H:i') ?? 'N/A' }}</td>
                    <td>
                        @if ($foundLecturerForMeeting)
                        @php
                        $isFilled = $baps->contains($i);
                        $isAuthLecturerForThisMeeting = ($authLecturerId == $currentLecturer->id);
                        $canFillThisMeeting = $allPreviousFilled[$i] ?? false;
                        @endphp

                        @if ($isAuthLecturerForThisMeeting)
                        @if ($isFilled)
                        <span class="btn btn-success" data-toggle="tooltip" data-placement="top"
                            title="BAP Pertemuan {{ $i }} sudah diisi">
                            <em class="icon ni ni-check-circle"></em>
                        </span>
                        <a href="{{ route('lecturer.bap.create', ['id' => $jadwal->id, 'pertemuan' => $i]) }}"
                            class="btn btn-warning" data-toggle="tooltip" data-placement="top"
                            title="Edit BAP Pertemuan {{ $i }}">
                            <em class="icon ni ni-edit"></em>
                        </a>
                        @else
                        @if ($canFillThisMeeting)
                        <a class="btn btn-dark" data-toggle="tooltip" data-placement="top"
                            title="Isi BAP Pertemuan {{ $i }}"
                            href="{{ route('lecturer.bap.create', ['id' => $jadwal->id, 'pertemuan' => $i]) }}">
                            <em class="icon ni ni-edit"></em>
                        </a>
                        @else
                        <span class="btn btn-secondary disabled" data-toggle="tooltip" data-placement="top"
                            title="BAP pertemuan sebelumnya belum diisi atau Anda bukan dosen pengampu untuk pertemuan ini">
                            <em class="icon ni ni-lock-alt"></em>
                        </span>
                        @endif
                        @endif
                        @else
                        @if ($isFilled)
                        <span class="btn btn-info disabled" data-toggle="tooltip" data-placement="top"
                            title="BAP sudah diisi oleh dosen pengampu lainnya">
                            <em class="icon ni ni-check-circle"></em>
                        </span>
                        @else
                        <span class="btn btn-secondary disabled" data-toggle="tooltip" data-placement="top"
                            title="Anda bukan dosen pengampu untuk pertemuan ini">
                            <em class="icon ni ni-lock-alt"></em>
                        </span>
                        @endif
                        @endif
                        @else
                        <span class="btn btn-secondary disabled" data-toggle="tooltip" data-placement="top"
                            title="Tidak ada dosen yang terhubung untuk pertemuan ini">
                            <em class="icon ni ni-lock-alt"></em>
                        </span>
                        @endif
                    </td>
                    </tr>
                    @endfor
            </tbody>
        </table>
        <a href="{{ route('lecturer.bap.index') }}" class="btn btn-outline-secondary" data-toggle="tooltip"
            data-placement="top" title="Kembali ke daftar kelas"><em class="icon ni ni-reply"></em> Back</a>
    </div>

    <x-custom.sweet-alert />

</x-main-layout>
