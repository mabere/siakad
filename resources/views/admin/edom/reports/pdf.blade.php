<!DOCTYPE html>
<html>

<head>
    <title>Laporan EDOM</title>
    <style>
        body {
            font-family: "Times New Roman";
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-top:-20px;margin-bottom: 20px;">
        <table class="kop" style="width: 100%;">
            <tr>
                <td class="logo1" style="width: 13%; text-align: left;border:none">
                    <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="90%" />
                </td>
                <td class="info" style="width: 74%; text-align: center;border:none">
                    <span style="font-size: 1.5rem"><strong>{{ $yayasan }}</strong></span>
                    <span style="font-size: 1.4rem"><strong>{{ $universitas }}</strong></span><br>
                    <span style="font-size: 1.3rem"><strong>LEMBAGA PENJAMINAN MUTU (LPM)</strong><br></span>
                    <span style="font-size: .9rem">Website: https://lpm.unilaki.ac.id | Email:
                        lmp@unilaki.ac.id<br>
                        Alamat: {{ $alamat }} | Telp: {{ $telp }}<br> {{ $kab }}</span>
                </td>
                <td class="logo2" style="width: 13%; text-align: right;border:none">
                    <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="90%" />
                </td>
            </tr>
        </table>
    </div>
    <hr style="margin-top:-3rem;border-bottom: 2px solid black;">
    <div class="header">
        <h2>Laporan Evaluasi Dosen Oleh Mahasiswa</h2>
        <p>Tahun Akademik: {{ $academicYear->ta }} - {{ $academicYear->semester }}</p>
    </div>

    <h3>Rata-rata Penilaian per Kategori</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryAverages as $category)
            <tr>
                <td>{{ $category->category }}</td>
                <td>{{ number_format($category->average, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Detail Penilaian Dosen</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Dosen</th>
                <th>Program Studi</th>
                <th>Jumlah MK</th>
                <th>Jumlah Responden</th>
                <th>Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lecturerAverages as $lecturer)
            <tr>
                <td>{{ $lecturer->nama_dosen }}</td>
                <td>{{ $lecturer->nama ?? 'N/A' }}</td>
                <td>{{ $lecturer->course_count }}</td>
                <td>{{ $lecturer->student_count }}</td>
                <td>{{ number_format($lecturer->average, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>

</html>