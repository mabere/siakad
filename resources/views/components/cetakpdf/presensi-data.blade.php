@props(['jadwal', 'ta', 'baps', 'attendances','signatures'])

{{-- Data Mata Kuliah --}}

<h3 style="text-align: center;"><u>DAFTAR HADIR PERKULIAHAN</u><br>ANGKATAN {{
    $jadwal->kelas->angkatan }}</h3>
<table border="0" cellpadding="4" cellspacing="0" style="width: 100%">
    <tr>
        <td>Mata Kuliah</td>
        <td>:</td>
        <td>{{ $jadwal->schedulable->name ?? 'N/A' }}</td>
        <td>Tahun Akademik</td>
        <td>:</td>
        <td>{{ $ta->ta }}/{{ $ta->semester ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>SKS</td>
        <td>:</td>
        <td>{{ $jadwal->schedulable->sks ?? 'N/A' }}</td>
        <td>Program Studi</td>
        <td>:</td>
        <td>
            @if ($jadwal->schedulable_type === 'App\Models\Course' && $jadwal->schedulable->department)
            {{ $jadwal->schedulable->department->nama ?? 'N/A' }}
            @else
            Mata Kuliah Dasar Umum
            @endif
        </td>
    </tr>
    <tr>
        <td>Angkatan/Semester</td>
        <td>:</td>
        <td>{{ $jadwal->kelas->angkatan }}/{{ $jadwal->schedulable->semester_number ?? 'N/A' }}</td>
        <td>Dosen</td>
        <td>:</td>
        <td>
            @foreach($jadwal->lecturersInSchedule as $index => $lecturer)
            {{ $index+1 }}. {{ $lecturer->nama_dosen }}<br>
            @endforeach
        </td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $jadwal->kelas->name }}</td>

    </tr>
</table>

{{-- Rekap Kehadiran --}}
<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; margin-top: 20px;">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">NIM</th>
            <th rowspan="2">Nama</th>
            <th colspan="16">Pertemuan</th>
            <th rowspan="2">%</th>
        </tr>
        <tr>
            @for($i = 1; $i <= 16; $i++) <th>{{ $i }}</th>
                @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $index => $attendance)
        <tr>
            <td style="text-align: center">{{ $index + 1 }}.</td>
            <td style="text-align: center">{{ $attendance->student->nim ?? 'N/A' }}</td>
            <td>{{ $attendance->student->nama_mhs ?? 'N/A' }}</td>
            @for ($i = 1; $i <= 16; $i++) @php $attendanceDetail=$attendance->
                attendanceDetails->firstWhere('meeting_number', $i);
                $status = $attendanceDetail->status ?? 'Tanpa Keterangan';
                $icons = [
                'Tanpa Keterangan' => '<span style="color: red;font-family: DejaVu Sans">&#10006;</span>',
                'Izin' => '<span style="color: orange;font-family: DejaVu Sans">&#x2139;</span>',
                'Sakit' => '<span style="color: blue;font-family: DejaVu Sans">s</span>',
                'Hadir' => '<span style="color: green;font-family: DejaVu Sans">&#10004;</span>',

                ];
                $display = $icons[$status] ?? '-';
                @endphp
                <td style="text-align: center;">{!! $display !!}</td>
                @endfor
                <td style="text-align: center;">{{ number_format(($attendance->attendanceDetails->where('status',
                    'Hadir')->count() / 16) * 100, 2) }}%</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="20">Keterangan:
                <span style="color: green;font-family: DejaVu Sans;">&#10004;</span><span
                    style="font-family: Arial;margin-right:20px">: Hadir</span>
                <span style="color: orange;font-family: DejaVu Sans;">&#x2139;</span><span
                    style="font-family: Arial;margin-right:20px">: Ijin</span>
                <span style="color: blue;font-family: DejaVu Sans;">s</span><span
                    style="font-family: Arial;margin-right:20px">: Sakit</span>
                <span style="color: red;font-family: DejaVu Sans;">&#x2715;</span><span
                    style="font-family: Arial;margin-right:20px">: Alpa</span>
            </td>
        </tr>
    </tbody>
</table>
