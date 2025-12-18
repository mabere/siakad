@props(['items', 'ta', 'signatures'])

@php
$jadwal = $items->first()->schedule ?? null;
@endphp

@if ($jadwal)
<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 15%;">Mata Kuliah</td>
        <td style="width: 2%;">:</td>
        <td style="width: 33%;">
            @if($jadwal->schedulable_type === 'App\Models\Course')
            {{ $jadwal->course->code ?? 'N/A' }}-{{ $jadwal->course->name ?? 'N/A' }}
            @elseif($jadwal->schedulable_type === 'App\Models\MkduCourse')
            {{ $jadwal->mkduCourse->code ?? 'N/A' }}-{{ $jadwal->mkduCourse->name ?? 'N/A' }}
            @else
            N/A
            @endif
        </td>
        <td style="width: 15%;">Program Studi</td>
        <td style="width: 2%;">:</td>
        <td style="width: 33%;">
            @if($jadwal->schedulable_type === 'App\Models\Course' && $jadwal->course->department)
            {{ $jadwal->course->department->nama ?? 'N/A' }}
            @else
            Mata Kuliah Dasar Umum
            @endif
        </td>
    </tr>
    <tr>
        <td>Dosen</td>
        <td>:</td>
        <td>
            @foreach($jadwal->lecturersInSchedule as $index => $lecturer)
            {{ $lecturer->nama_dosen ?? 'N/A' }}{{ !$loop->last ? ',' : '' }}
            @endforeach
        </td>
        <td>Tahun Akademik</td>
        <td>:</td>
        <td>{{ $ta->ta ?? 'N/A' }}/{{ $ta->semester ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Kelas/Semester</td>
        <td>:</td>
        <td>
            {{ $jadwal->kelas->name ?? 'N/A' }}/
            @if($jadwal->schedulable_type === 'App\Models\Course')
            {{ $jadwal->course->smt ?? 'N/A' }}
            @else
            N/A
            @endif
        </td>
        <td>SKS</td>
        <td>:</td>
        <td>
            @if($jadwal->schedulable_type === 'App\Models\Course')
            {{ $jadwal->course->sks ?? 'N/A' }}
            @else
            N/A
            @endif
        </td>
    </tr>
</table>

<table class="table">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">NIM</th>
            <th rowspan="2">Nama</th>
            <th colspan="16">Pertemuan Ke-</th>
            <th rowspan="2">% Kehadiran</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 16; $i++) <th>{{ $i }}</th> @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($items as $index => $attendance)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}.</td>
            <td style="text-align: center;">{{ $attendance->student->nim ?? 'N/A' }}</td>
            <td>{{ $attendance->student->nama_mhs ?? 'N/A' }}</td>
            @for ($i = 1; $i <= 16; $i++) @php $attendanceDetail=$attendance->attendanceDetails->where('meeting_number',
                $i)->first();
                $status = $attendanceDetail ? $attendanceDetail->status : 'Tanpa Keterangan';
                $icons = ['Tanpa Keterangan' => '-', 'Izin' => 'I','Sakit' => 'S','Hadir' => 'H',];
                $display = $icons[$status] ?? '-';
                @endphp
                <td style="text-align: center;">{!! $display !!}</td>
                @endfor
                <td style="text-align: center;">{{ $attendance->persentase ?? 0 }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-danger text-center">Data jadwal tidak tersedia.</p>
@endif
