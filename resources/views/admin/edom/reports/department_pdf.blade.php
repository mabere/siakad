<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan EDOM {{ $department->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        .statistics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
        }

        h3 {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="kop" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <table class="kop" style="width: 100%;">
            <tr>
                <td class="logo1" style="width: 14%; text-align: left;">
                    <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="80%" />
                </td>
                <td class="info" style="width: 72%; text-align: center;">
                    <p style="line-height: 1.75rem">
                        <span style="font-size: 1.8rem"><b>{{ $yayasan }}</b></span><br>
                        <span style="font-size: 1.7rem"><b>{{ $universitas }}</b></span><br>
                        <span style="font-size: 1.5rem;text-transform: uppercase;"><b>FAKULTAS {{
                                $department->faculty->nama }}</b></span><br>
                        <span style="font-size: 1.4rem;text-transform: uppercase;"><b>PROGRAM STUDI {{
                                $department->nama}}</b></span><br>
                    </p>
                    <span>Website: {{ $department->website }}, Telp: {{ $department->telp }} | Email: {{
                        $department->email }}</span><br>
                    <span>Alamat: {{ $department->alamat }}, {{ $kab }}</span>
                </td>
                <td class="logo2" style="width: 14%; text-align: right;">
                    <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="80%" />
                </td>
            </tr>
        </table>
    </div>
    <hr style="margin-top:-3rem;border-bottom: 2px solid black;">

    <div class="header">
        <h2>Laporan Evaluasi Dosen Oleh Mahasiswa (EDOM)</h2>
        <h3>{{ $department->nama }}</h3>
        <p>Tahun Akademik: {{ $academicYear->ta }} - Semester {{ $academicYear->semester }}</p>
    </div>

    <div class="statistics">
        <table>
            <tr>
                <th>Total Mata Kuliah</th>
                <th>Total Mahasiswa</th>
                <th>Total Dosen</th>
                <th>Response Rate</th>
            </tr>
            <tr>
                <td>{{ $statistics['total_courses'] }}</td>
                <td>{{ $statistics['total_students'] }}</td>
                <td>{{ $statistics['total_lecturers'] }}</td>
                <td>{{ number_format($statistics['response_rate'], 2) }}%</td>
            </tr>
        </table>
    </div>

    <h3>Rata-rata Per Kategori</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Rata-rata</th>
                <th>Jumlah MK</th>
                <th>Jumlah Mahasiswa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryAverages as $category)
            <tr>
                <td>{{ $category->category }}</td>
                <td>{{ number_format($category->average, 2) }}</td>
                <td>{{ $category->course_count }}</td>
                <td>{{ $category->student_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Rata-rata Per Dosen</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Dosen</th>
                <th>Rata-rata</th>
                <th>Jumlah MK</th>
                <th>Jumlah Mahasiswa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lecturerAverages as $lecturer)
            <tr>
                <td>{{ $lecturer->nama_dosen }}</td>
                <td>{{ number_format($lecturer->average, 2) }}</td>
                <td>{{ $lecturer->course_count }}</td>
                <td>{{ $lecturer->student_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>