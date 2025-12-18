<x-main-layout>
    @section('title', 'Edit Presensi')
    <style>
        .gradient-header {
            background: linear-gradient(135deg, #04927f 0%, #09cbce 100%);
        }

        .btn-gradient-primary {
            background: linear-gradient(45deg, #4b6cb7, #182848);
            color: white;
            border: none;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, #3b5b9d, #071528);
            color: white;
        }

        .btn-gradient-purple {
            background: linear-gradient(45deg, #8E2DE2, #4A00E0);
            color: white;
            border: none;
        }

        .btn-gradient-purple:hover {
            background: linear-gradient(45deg, #7d1dcf, #3a00b3);
        }

        .progress {
            background: linear-gradient(45deg, #c7b5d6, #775bb469);
        }

        .progress-bar-label {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(18, 49, 204, 0.3);
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-lg mb-4">
                    <div class="card-header gradient-header text-white pb-3">
                        <h1 class="h3 mb-0 font-weight-bold">@yield('title')</h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Mata Kuliah --}}
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-light rounded p-3">
                                    <small class="text-muted d-block mb-1">Mata Kuliah</small>
                                    <span class="d-block font-weight-bold text-dark">
                                        {{ $jadwal->course_name ?? 'N/A' }} ({{ $jadwal->course_code ?? 'N/A' }})
                                    </span>
                                </div>
                            </div>

                            {{-- Program Studi (Kondisional) --}}
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-light rounded p-3">
                                    <small class="text-muted d-block mb-1">Program Studi</small>
                                    <span class="d-block font-weight-bold text-dark">
                                        @if($jadwal->schedulable_type === 'App\Models\Course' &&
                                        $jadwal->course->department)
                                        {{ $jadwal->course->department->nama }}
                                        @else
                                        MKDU / Tidak Berlaku
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- SKS (Kondisional) --}}
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-light rounded p-3">
                                    <small class="text-muted d-block mb-1">SKS</small>
                                    <span class="d-block font-weight-bold text-dark">
                                        @if($jadwal->schedulable_type === 'App\Models\Course')
                                        {{ $jadwal->course->sks ?? 'N/A' }} SKS
                                        @else
                                        N/A
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Semester (Kondisional) --}}
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-light rounded p-3">
                                    <small class="text-muted d-block mb-1">Semester</small>
                                    <span class="d-block font-weight-bold text-dark">
                                        @if($jadwal->schedulable_type === 'App\Models\Course')
                                        {{ $jadwal->course->semester_number ?? 'N/A' }}
                                        @else
                                        N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 position-relative">
                    @php
                    $totalMeetings =
                    $jadwal->attendances->flatMap->attendanceDetails->unique('meeting_number')->count();
                    $progress = ($totalMeetings / 16) * 100;
                    @endphp
                    <div class="progress"
                        style="height: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(124, 8, 224, 0.1);">
                        <div class="progress-bar bg-info progress-bar-striped" role="progressbar"
                            style="width: {{ $progress }}%;" aria-valuenow="{{ $totalMeetings }}" aria-valuemin="0"
                            aria-valuemax="16">
                            <span class="progress-bar-label">{{ $totalMeetings }}/16 Pertemuan Terisi</span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg">
                    <div class="table-responsive rounded">
                        <table class="table table-hover mb-0">
                            <thead class="gradient-header text-white">
                                <tr>
                                    <th class="text-center">Pertemuan</th>
                                    <th>Dosen Pengampu</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(range(1, 16) as $i)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">#{{ $i }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold text-dark">
                                                @php
                                                $assignee = null;
                                                foreach ($jadwal->lecturersInSchedule as $lect) {
                                                if (($i >= ($lect->pivot->start_pertemuan ?? 1)) && ($i <= ($lect->
                                                    pivot->end_pertemuan ?? 16))) {
                                                    $assignee = $lect;
                                                    break;
                                                    }
                                                    }
                                                    @endphp
                                                    {{ $assignee->nama_dosen ?? 'N/A' }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $jadwal->hari }} • {{ $jadwal->start_time->format('H:i') . '-' .
                                                $jadwal->end_time->format('H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @php
                                        $isPresensiFilled =
                                        $jadwal->attendances->flatMap->attendanceDetails->where('meeting_number',
                                        $i)->isNotEmpty();
                                        @endphp
                                        <span
                                            class="badge badge-pill {{ $isPresensiFilled ? 'bg-success' : 'bg-warning' }}">
                                            {{ $isPresensiFilled ? 'Telah Diisi' : 'Menunggu' }}
                                        </span>
                                    </td>

                                    <td class="text-center align-middle">
                                        @php
                                        $isPresensiFilled =
                                        $jadwal->attendances->flatMap->attendanceDetails->where('meeting_number',
                                        $i)->isNotEmpty();
                                        // Untuk pertemuan pertama (i=1), selalu anggap "sudah diisi" agar bisa dimulai
                                        $isPreviousFilled = ($i === 1) ? true :
                                        $jadwal->attendances->flatMap->attendanceDetails->where('meeting_number', $i -
                                        1)->isNotEmpty();

                                        $lastUpdatedDetail =
                                        $jadwal->attendances->flatMap->attendanceDetails->where('meeting_number',
                                        $i)->first();
                                        $lastUpdated = $lastUpdatedDetail ?
                                        $lastUpdatedDetail->updated_at->format('d/m/Y H:i') : 'N/A';

                                        $baseButtonClass = ($i <= 8) ? 'btn-primary' : 'btn-purple' ;
                                            $buttonClass=$isPresensiFilled ? 'btn-success' : 'btn-warning' ;
                                            $buttonText=$isPresensiFilled ? 'Lihat/Edit' : 'Isi Presensi' ;
                                            $tooltipText=$isPresensiFilled ? "Terakhir diedit: " . $lastUpdated
                                            : 'Presensi belum diisi' ; @endphp <div class="btn-group" role="group">
                                            {{-- Aktifkan tombol jika pertemuan sebelumnya sudah diisi --}}
                                            @if ($isPreviousFilled || $isPresensiFilled)
                                            <a href="{{ route('lecturer.attendance.input', ['id' => $jadwal->id, 'pertemuan' => $i]) }}"
                                                class="btn {{ $baseButtonClass }} {{ $buttonClass }} btn-sm"
                                                data-toggle="tooltip" title="{{ $tooltipText }}">
                                                <i class="icon ni ni-edit"></i> {{ $buttonText }}
                                            </a>
                                            {{-- Tombol QR Code --}}
                                            <a href="{{ route('lecturer.attendance.qr', ['id' => $jadwal->id, 'pertemuan' => $i]) }}"
                                                class="btn {{ $baseButtonClass }} btn-info btn-sm ml-1 btn-qr"
                                                data-toggle="tooltip" title="Generate QR Code untuk Pertemuan {{ $i }}">
                                                <i class="icon ni ni-qr"></i> QR
                                            </a>
                                            @else
                                            <button class="btn {{ $baseButtonClass }} btn-secondary btn-sm" disabled
                                                data-toggle="tooltip"
                                                title="Harap isi presensi pertemuan ke-{{ $i - 1 }} terlebih dahulu">
                                                <i class="icon ni ni-lock"></i> Terkunci
                                            </button>
                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-custom.sweet-alert />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @push('scripts')
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip({
                placement: 'top',
                trigger: 'hover'
            });
            $('.btn-qr').on('click', function(e) {
                e.preventDefault();
                window.open($(this).attr('href'), '_blank', 'width=600,height=600');
            });
        });
    </script>
    @endpush
</x-main-layout>
