<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Kegiatan Penunjang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            text-decoration: underline
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .info {
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
        }

        .filter-info {
            margin-bottom: 10px;
            font-style: italic;
        }

        h4.fakultas {
            text-transform: uppercase;
            font-size: .85rem
        }

        h2.yayasan {
            font-size: 1.2rem !important;
            margin: 0 !important;
        }

        h3.universitas {
            font-size: 1.1rem !important;
            margin: 0 !important;
        }

        h4.fakultas {
            font-size: 1rem !important;
            margin: 0 !important;
        }

        .header,
        p.alamat {
            margin: 0 !important
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 class="yayasan">{{ $yayasan }}</h2>
        <h3 class="universitas">{{ $universitas }}</h3>
        <h4 class="fakultas">FAKULTAS {{ $lecturer->faculty->nama }}</h4>
        <p class="alamat"> | Telp: {{ $telp }}<br>Alamat: {{ $alamat }}, {{ $kab }}</p>
    </div>
    <hr>
    <div class="title">DATA KEGIATAN PENUNJANG DOSEN</div>
    <div class="subtitle">{{ config('app.name') }}</div>

    <div class="info">
        <table style="width: 50%; border: none; margin-bottom: 20px;">
            <tr>
                <td style="border: none; width: 100px;">Nama</td>
                <td style="border: none; width: 10px;">:</td>
                <td style="border: none;">{{ $lecturer->nama_dosen }}</td>
            </tr>
            <tr>
                <td style="border: none;">NIDN</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $lecturer->nidn }}</td>
            </tr>
            <tr>
                <td style="border: none;">Tanggal Cetak</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ date('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    @if(isset($filters) && count(array_filter($filters)) > 0)
    <div class="filter-info">
        Filter yang digunakan:
        @if(isset($filters['search']))
        Pencarian: "{{ $filters['search'] }}"
        @endif
        @if(isset($filters['level']))
        Level: {{ $filters['level'] }}
        @endif
        @if(isset($filters['start_date']))
        Dari: {{ $filters['start_date'] }}
        @endif
        @if(isset($filters['end_date']))
        Sampai: {{ $filters['end_date'] }}
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul Kegiatan</th>
                <th>Penyelenggara</th>
                <th>Level</th>
                <th>Peran</th>
                <th>Tanggal</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penunjangs as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->organizer }}</td>
                <td style="text-align: center;">{{ $item->level }}</td>
                <td>{{ $item->peran }}</td>
                <td style="text-align: center;">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                <td>
                    @if(filter_var($item->proof, FILTER_VALIDATE_URL))
                    <a href="{{ $item->proof }}">Link Dokumen</a>
                    @else
                    File: {{ $item->proof }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>