@props(['jadwal', 'ta', 'baps', 'attendances','signatures'])

{{-- Data Mata Kuliah --}}
<x-kop.prodi :prodi="$jadwal->kelas->department->nama" :fakultas="$jadwal->kelas->department->faculty->nama"
    :alamat="'Jl. Sultan Hasanuddin No. 234, Unaaha'" :nomorTelepon="'(0401) 123456'"
    :website="'https://fkip.unilaki.ac.id'" :email="'info@fkip.unilaki.ac.id'" :logoUri="$logoUri" :logoUri2="$logoUri2"
    style="page-break-before: always;" />
<h3 style="text-align: center;"><u>DAFTAR HADIR PERKULIAHAN</u><br>ANGKATAN {{
    $jadwal->kelas->angkatan }}</h3>
<table border="0" cellpadding="4" cellspacing="0" style="width: 100%">
    <tr>
        <td>Mata Kuliah</td>
        <td>:</td>
        <td>{{ $jadwal->schedulable->name ?? 'N/A' }}</td>
        <td>Tahun Akademik</td>
        <td>:</td>
        <td>{{ $ta->ta }}/({{ $ta->semester ?? 'N/A' }})</td>
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
                'Tanpa Keterangan' => '-',
                'Izin' => 'I',
                'Sakit' => 'S',
                'Hadir' => 'H',
                ];
                $display = $icons[$status] ?? '-';
                @endphp
                <td style="text-align: center;">{{ $display }}</td>
                @endfor
                <td style="text-align: center;">{{ number_format(($attendance->attendanceDetails->where('status',
                    'Hadir')->count() / 16) * 100, 2) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>


<x-cetakpdf.signature :signatures="$signatures" />

{{-- Data BAP --}}
<div style="page-break-before: always;">
    <x-kop.prodi :prodi="$jadwal->kelas->department->nama" :fakultas="$jadwal->kelas->department->faculty->nama"
        :alamat="'Jl. Sultan Hasanuddin No. 234, Unaaha'" :nomorTelepon="'(0408) 2421-777'"
        :website="'https://fkip.unilaki.ac.id'" :email="'info@fkip.unilaki.ac.id'" :logoUri="$logoUri"
        :logoUri2="$logoUri2" style="page-break-before: always;" />
    <h3 style="text-align: center;"><u>BERITA ACARA PERKULUAHAN (BAP)</u><br>ANGAKATAN {{
        $jadwal->kelas->angkatan }}</h3>
    <table border="0" cellpadding="4" cellspacing="0" style="width: 100%">
        <tr>
            <td>Mata Kuliah</td>
            <td>:</td>
            <td>{{ $jadwal->schedulable->name ?? 'N/A' }}</td>
            <td>Tahun Akademik</td>
            <td>:</td>
            <td>{{ $ta->ta }}/({{ $ta->semester ?? 'N/A' }})</td>
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
    <table border="1" cellpadding="4" cellspacing="0" style="width: 100%; margin-top: 20px;">
        <thead>
            <tr>
                <th>Minggu</th>
                <th>Tanggal</th>
                <th>Dosen</th>
                <th>Topik/Materi</th>
                <th>Keterangan</th>
                <th>Hadir</th>
                <th>Paraf</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 16; $i++) @php $bap=$baps->where('pertemuan', $i)->first();

                $jumlahHadir = $attendances->filter(function($attendance) use ($i) {
                $detail = $attendance->attendanceDetails->firstWhere('meeting_number', $i);
                return $detail && $detail->status === \App\Models\Attendance::PRESENT;
                })->count();

                // Tentukan dosen berdasarkan siapa yang mengisi BAP jika ada,
                // jika tidak ada BAP, ambil dari jadwal lecturersInSchedule.
                // Ini lebih akurat karena BAP mencatat lecturer_id.
                $dosenBAP = $bap ? $bap->lecturer->nama_dosen : null;

                // Jika BAP belum ada, atau lecturer_id di BAP tidak diisi,
                // baru gunakan logika pembagian dosen 1-8 dan 9-16
                if (!$dosenBAP) {
                $dosenUntukPertemuan = null;
                foreach ($jadwal->lecturersInSchedule as $lecturerSchedule) {
                if ($i >= $lecturerSchedule->pivot->start_pertemuan && $i <= $lecturerSchedule->pivot->end_pertemuan) {
                    $dosenUntukPertemuan = $lecturerSchedule->nama_dosen;
                    break;
                    }
                    }
                    $dosenBAP = $dosenUntukPertemuan;
                    }

                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $i }}.</td>
                        <td style="text-align: center">{{ $bap ? $bap->created_at->format('d/m/y') : '-' }}</td>
                        <td width="150px">{{ $dosenBAP ?? '-' }}</td>
                        <td>{{ $bap ? $bap->topik : '-' }}</td>
                        <td>{{ $bap ? $bap->keterangan : '-' }}</td>
                        <td style="text-align: center">{{ $jumlahHadir }}</td>
                        <td></td>
                    </tr>
                    @endfor
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total Hadir Keseluruhan:</strong></td>
                <td style="text-align: center;"><strong>{{ $attendances->sum(function($a) {
                        return $a->attendanceDetails->where('status', \App\Models\Attendance::PRESENT)->count();
                        }) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>