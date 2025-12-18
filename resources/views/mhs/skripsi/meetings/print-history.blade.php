<!DOCTYPE html>
<html>

<head>
    <title>Riwayat Bimbingan Thesis</title>
    <style>
        @page {
            size: A4;
            margin: 1cm 2.54cm 2.54cm 2.54cm;
        }

        body {
            font-family: "Times New Roman";
            /* line-height: 1.6; */
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 5px;
        }

        .kop {
            width: 100%;
            border-collapse: collapse;
        }

        .kop td {
            text-align: center;
            vertical-align: middle;
            border: none;
        }

        .logo1,
        .logo2 {
            width: 15%;
        }

        .logo1 img,
        .logo2 img {
            max-width: 100px;
            height: auto;
        }

        .info {
            width: 70%;
        }

        .info h4,
        .info h2,
        .info h3,
        .info h5 {
            margin: 5px 0;
        }

        .student-info {
            margin-bottom: 30px;
            line-height: 2;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 2px 0;
            border: none;
            font-size: 14px
        }

        .student-info td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .meeting-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .meeting-table th,
        .meeting-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .meeting-table th {
            background-color: #f8f9fa;
            text-align: center;
        }

        .supervisor-title {
            font-weight: bold;
            margin: 20px 0 10px;
            font-size: 14px;
        }

        .page-title {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="kop">
            <tr>
                <td class="logo1"><img src="{{ $logoUri }}" alt="Logo"></td>
                <td class="info">
                    <h2>{{ $yay }}</h2>
                    <h3>{{ $univ }}</h3>
                    <h4>FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</h4>
                    <h4>PROGRAM STUDI PENDIDIKAN BAHASA INGGRIS</h4>
                    <h5>Jl. Sultan Hasanuddin No. 234, Unaaha; Website: https://pbi.fkip-uilaki.ac.id</h5>
                </td>
                <td class="logo2"><img src="{{ $logoUri2 }}" alt="Logo"></td>
            </tr>
        </table>
        <hr>
    </div>
    <div class="page-title"><u>RIWAYAT BIMBINGAN SKRIPSI</u></div>

    <div class="student-info">
        <table>
            <tr>
                <td>Nama</td>
                <td>: {{ $student->nama_mhs }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>: {{ $student->nim }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $student->department->nama }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>: {{ $thesis->title }}</td>
            </tr>
        </table>
    </div>

    <div class="supervisor-section">
        <div class="supervisor-title">Pembimbing 1: {{ $thesis->supervisions->where('supervisor_role',
            'pembimbing_1')->first()->supervisor->nama_dosen }}</div>
        <table class="meeting-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Uraian Bimbingan</th>
                    <th width="35%">Catatan Pembimbing</th>
                    <th width="35%">Tanggal</th>
                    <th width="15%">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supervisor1Meetings as $index => $meeting)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $meeting->description }}</td>
                    <td>{{ $meeting->notes }}</td>
                    <td>{{ $meeting->meeting_date }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada riwayat bimbingan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="supervisor-section">
        <div class="supervisor-title">Pembimbing 2: {{ $thesis->supervisions->where('supervisor_role',
            'pembimbing_2')->first()->supervisor->nama_dosen }}</div>
        <table class="meeting-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Uraian Bimbingan</th>
                    <th width="35%">Catatan Pembimbing</th>
                    <th width="35%">Tanggal</th>
                    <th width="15%">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supervisor2Meetings as $index => $meeting)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $meeting->description }}</td>
                    <td>{{ $meeting->notes }}</td>
                    <td>{{ $meeting->meeting_date }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada riwayat bimbingan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <button class="no-print" onclick="window.print()">Cetak</button>
</body>

</html>