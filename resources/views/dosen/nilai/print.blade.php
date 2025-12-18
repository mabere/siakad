<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Siakad | Print Nilai</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend') }}/assets/images/favicon.png">
    <link href="{{ asset('backend') }}/dist/css/style.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 120px;
        }

        .header h1,
        .header h2,
        .header h3,
        .header h5 {
            margin: 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 5px;
            text-align: left;
        }

        .info-table th {
            width: 20%;
        }

        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .grade-table th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        .grade-table td {
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature p {
            margin: 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        @media print {
            .footer {
                page-break-after: always;
            }
        }

        .header {
            text-align: center;
            padding: 20px;
        }

        .header .row {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header .col-2 {
            display: flex;
            justify-content: center;
            margin-right: 13px;
            margin-left: 13px;
        }

        .header .col-2 img {
            max-width: 100px;
            /* Sesuaikan ukuran logo */
            height: auto;
        }

        .header .col-8 h1,
        .header .col-8 h2,
        .header .col-8 h3,
        .header .col-8 h5 {
            margin: 5px 0;
        }

        h3.text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="row">
                <div class="col-2">
                    <img src="https://lb.test/images/logox.png" alt="Logox">
                </div>
                <div class="col-8">
                    <h1>{{ $yay }}</h1>
                    <h2>{{ $univ }}</h2>
                    <h3>FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</h3>
                    <h3>PENDIDIKAN BAHASA INGGRIS</h3>
                    <h5>Jl. Sultan Hasanuddin No. 234, Unaaha; Website: https://pbi.fkip-uilaki.ac.id</h5>
                </div>
                <div class="col-2">
                    <img src="https://lb.test/images/tutwuri.png" alt="Logox">
                </div>
            </div>
        </div>

        <hr>

        <h3 class="text-center"><b>DAFTAR NILAI MAHASISWA</b><br>ANGKATAN: {{$jadwal->kelas->angkatan}}</h3>

        <table class="info-table">
            <tr>
                <th>Mata Kuliah</th>
                <td>:</td>
                <td>{{$jadwal->course->name}}</td>
                <th>Fakultas</th>
                <td>:</td>
                <td>{{$jadwal->course->department->faculty->nama}}</td>
            </tr>
            <tr>
                <th>Lecturer</th>
                <td>:</td>
                <td>@php
                    $lecturers = $jadwal->lecturersInSchedule->pluck('nama_dosen')->toArray();
                    $count = count($lecturers);

                    if ($count === 1) {
                    echo $lecturers[0];
                    } elseif ($count === 2) {
                    echo $lecturers[0] . ' & ' . $lecturers[1];
                    } else {
                    $lastLecturer = array_pop($lecturers);
                    echo implode(', ', $lecturers) . ', & ' . $lastLecturer;
                    }
                    @endphp</td>
                <th>Program Studi</th>
                <td>:</td>
                <td>{{$jadwal->course->department->nama}}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>:</td>
                <td>{{$jadwal->kelas->name}}</td>
                <th>TA/Semester</th>
                <td>:</td>
                <td>{{$ta->ta}}/{{$ta->semester}}</td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td>:</td>
                <td>{{$jadwal->hari}}, {{$jadwal->waktu}}</td>
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
                    <td style="text-align:left">{{ $item->student->nama_mhs }}</td>
                    <td>{{ $item->attendance }}</td>
                    <td>{{ $item->participation }}</td>
                    <td>{{ $item->assignment }}</td>
                    <td>{{ $item->mid }}</td>
                    <td>{{ $item->final }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $item->nhuruf }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-danger">Data student belum tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature">
            <p>Unaaha, {{$tgl}}<br>Dosen Pengampu</p>
            <br><br>
            <p><u><b>{{$jadwal->lecturer->nama_dosen}}</b></u><br>NIDN. {{$jadwal->lecturer->nidn}}</p>
        </div>

        <div class="footer">
            <p>All Rights Reserved by Matrix-admin. Designed and Developed by <a
                    href="https://www.wrappixel.com">WrapPixel</a>.</p>
        </div>
    </div>
</body>

</html>
