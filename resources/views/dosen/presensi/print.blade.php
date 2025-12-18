<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Siakad | Cetak Daftar Hadir Perkuliahan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid black;
        }

        .header img {
            width: 100px;
        }

        .header h1,
        .header h2,
        .header h3,
        .header h5 {
            margin: 5px 0;
        }

        .info-table,
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-detail th,
        .info-detail td {
            padding: 5px;
            text-align: left;
        }

        .grade-table th,
        .grade-table td {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .grade-table th {
            background-color: gray;
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

        @media print {
            .container {
                page-break-after: always;
            }
        }

        .text-center {
            text-align: center;
        }

        .logo1 {
            margin-right: 20px
        }

        .teks {
            text-align: center
        }

        td.kosong {
            padding: 20px
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="kop">
                <tr>
                    <td style="margin-right:20px" class="logo1">
                        <img src="{{ $logoUri }}" alt="Logo" style="width: 68px">
                    </td>
                    <td class="kosong"></td>
                    <td class="teks">
                        <h1>{{ $yay }}</h1>
                        <h2>{{ $univ }}</h2>
                        <h3>FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</h3>
                        <h3>PENDIDIKAN BAHASA INGGRIS</h3>
                        <h5>Jl. Sultan Hasanuddin No. 234, Unaaha; Website: https://pbi.fkip-uilaki.ac.id</h5>
                    </td>
                    <td class="kosong"></td>
                    <td class="logo2">
                        <img src="{{ $logoUri2 }}" alt="Logo" style="width: 68px">
                    </td>
                </tr>
            </table>
        </div>

        <h3 class="text-center"><b>DAFTAR HADIR PERKULIAHAN</b><br>ANGKATAN: {{$jadwal->kelas->angkatan}}</h3>
        <table class="info-detail">
            <tr>
                <th>Mata Kuliahs</th>
                <td>:</td>
                <td>{{$jadwal->course->name}}</td>
                <th>Fakultas</th>
                <td>:</td>
                <td>{{$jadwal->course->department->faculty->nama}}</td>
            </tr>
            <tr>
                <th>SKS</th>
                <td>:</td>
                <td>{{$jadwal->course->sks}}</td>
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
                <th>Dosen</th>
                <td>:</td>
            </tr>
        </table>
        <br><br>
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
        </div>
    </div>
</body>

</html>
