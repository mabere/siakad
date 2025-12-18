<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kegiatan Penunjang Tervalidasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR KEGIATAN PENUNJANG TERVALIDASI</h2>
    </div>

    <div class="info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 150px;">Nama Dosen</td>
                <td style="border: none;">: {{ $lecturer->nama_dosen }}</td>
            </tr>
            <tr>
                <td style="border: none;">NIDN</td>
                <td style="border: none;">: {{ $lecturer->nidn }}</td>
            </tr>
            @if($start_date && $end_date)
            <tr>
                <td style="border: none;">Periode</td>
                <td style="border: none;">: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</td>
            </tr>
            @endif
            @if($level)
            <tr>
                <td style="border: none;">Level</td>
                <td style="border: none;">: {{ $level }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Judul Kegiatan</th>
                <th>Penyelenggara</th>
                <th>Level</th>
                <th>Peran</th>
                <th>Tanggal Validasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penunjangs as $index => $penunjang)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($penunjang->date)->format('d/m/Y') }}</td>
                <td>{{ $penunjang->title }}</td>
                <td>{{ $penunjang->organizer }}</td>
                <td>{{ $penunjang->level }}</td>
                <td>{{ $penunjang->peran }}</td>
                <td>{{ $penunjang->validated_at ? \Carbon\Carbon::parse($penunjang->validated_at)->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data kegiatan tervalidasi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
