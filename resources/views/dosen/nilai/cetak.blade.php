<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Siakad | Print Nilai</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            margin: 1.5cm 0 .5cm 0;
        }

        h3 {
            text-align: center;
            line-height: 1.15rem;
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-detail th,
        .info-detail td {
            padding: 2px 8px;
            text-align: left;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .grade-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 1rem;
        }

        .center-signature {
            text-align: center;
            margin-top: 30px;
        }

        @page {
            size: A4;
            margin: 1.5cm;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>

</head>

<body>
    <x-kop.prodinilai :prodi="$jadwal->kelas->department->nama" :fakultas="$jadwal->kelas->department->faculty->nama"
        :nomorTelepon="'(0401) 123456'" :website="'https://fkip.unilaki.ac.id'" :email="'info@fkip.unilaki.ac.id'"
        :logoUri="$logoUri" :logoUri2="$logoUri2" />

    <h3><u><b>DAFTAR NILAI MAHASISWA</b></u><br>ANGKATAN: {{$jadwal->kelas->angkatan}}</h3>

    <table class="info-detail">
        <tr>
            <th>Mata Kuliah</th>
            <td>: {{$jadwal->schedulable->name}}</td>
            <th>Fakultas</th>
            <td>: {{$jadwal->kelas->department->faculty->nama}}</td>
        </tr>
        <tr>
            <th>SKS</th>
            <td>: {{$jadwal->course->sks}}</td>
            <th>Program Studi</th>
            <td>: {{$jadwal->kelas->department->nama}}</td>
        </tr>
        <tr>
            <th>Kelas</th>
            <td>: {{$jadwal->kelas->name}}</td>
            <th>TA/Semester</th>
            <td>: {{$ta->ta}}/{{$ta->semester}}</td>
        </tr>
        <tr>
            <th>Waktu</th>
            <td>: {{$jadwal->hari}}, {{ $jadwal->start_time->format('H:i') . '-' . $jadwal->end_time->format('H:i') }}
            </td>
            <th>Dosen</th>
            <td style="vertical-align: top;">
                <table>
                    <tr>
                        <td style="vertical-align: top;padding-left: 0;">:</td>
                        <td style="margin: 0; padding-left: 0;">
                            <ol style="margin: 0; padding-left: 0;">
                                @foreach ($jadwal->lecturersInSchedule as $dosen)
                                <li>{{ $dosen->nama_dosen }}</li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="grade-table">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">NIM</th>
                <th rowspan="2">Nama</th>
                <th colspan="5">Nilai</th>
                <th rowspan="2">Akhir</th>
                <th rowspan="2">Huruf</th>
            </tr>
            <tr>
                <th>Kehadiran</th>
                <th>Keaktifan</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->student->nim }}</td>
                <td style="text-align: left">{{ $item->student->nama_mhs }}</td>
                <td>{{ $item->attendance ?? 0 }}%</td>
                <td>{{ $item->participation ?? 0 }}</td>
                <td>{{ $item->assignment ?? 0 }}</td>
                <td>{{ $item->mid ?? 0 }}</td>
                <td>{{ $item->final ?? 0 }}</td>
                <td>{{ $item->total ?? 0 }}</td>
                <td>{{ $item->nhuruf ?? 'T/K' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;color:red">
                    Tidak ada data nilai. Kode Jadwal: {{ $id }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <p style="text-align: right; margin-right: 30px;">Unaaha, {{ $tgl }}</p>
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="width: 45%; text-align: center;">
                    <p style="margin-bottom: 4rem;font-weight:bold">Dosen I,</p>
                    @php
                    $firstLecturer = $jadwal->lecturersInSchedule->where('pivot.start_pertemuan', 1)->first();
                    @endphp
                    @if($firstLecturer)
                    <u><b>{{ $firstLecturer->nama_dosen }}</b></u><br>
                    NIDN. {{ $firstLecturer->nidn }}
                    @else
                    <p>Tidak tersedia</p>
                    @endif
                </td>
                <td style="width: 5%;"></td>
                <td style="width: 45%; text-align: center;">
                    <p style="margin-bottom: 4rem;font-weight:bold">Dosen II,</p>
                    @php
                    $secondLecturer = $jadwal->lecturersInSchedule->where('pivot.start_pertemuan', 9)->first();
                    @endphp
                    @if($secondLecturer)
                    <u><b>{{ $secondLecturer->nama_dosen }}</b></u><br>
                    NIDN. {{ $secondLecturer->nidn }}
                    @else
                    <p>Tidak tersedia</p>
                    @endif
                </td>
            </tr>
        </table>
        <div class="center-signature">
            <p style="font-weight:bold">Mengetahui,<br>Ketua Program Studi</p><br><br><br>
            <u><b>{{ $jadwal->kelas->department->kaprodi ?? 'Tidak tersedia' }}</b></u><br>
            NIDN. {{ $jadwal->kelas->department->nip ?? '' }}
        </div>
    </div>

</body>

</html>
