<!DOCTYPE html>
<html>

<head>
    <title>KARTU HASIL STUDI (KHS)</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid black;
        }

        .logo1 img {
            display: flex;
            width: 68px;
        }

        .header h2,
        .header h3,
        .header h4,
        .header h5 {
            margin: 5px 0;
        }

        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .table td,
        .table th {
            padding: 8px;
            border: 1px black solid;
        }

        .info {
            text-align: center;
        }

        td.kosong {
            padding: 0 20px
        }

        table.info-detail.td {
            padding: 8px;
        }

        table.info-detail.td:first-child {
            width: 30%;
        }

        table.info-detail.td:nth-child(2) {
            width: 5%;
        }

        table.info-detail.td:nth-child(3) {
            width: 30%;
        }

        td.tengah {
            padding: 0 50px
        }

        .table th {
            background-color: #28a745;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="kop">
                <tr>
                    <td class="logo1">
                        <img src="data:image/png;base64,{{ $logoUri ?? '' }}" alt="Logo Tut Wuri">
                    </td>
                    <td class="kosong"></td>
                    <td class="info">
                        <h2>{{ $yayasan }}</h2>
                        <h3>{{ $universitas }}</h3>
                        <h4 style="text-transform: uppercase;">FAKULTAS {{ $mahasiswa->department->faculty->nama ??
                            'Tidak tersedia' }}</h4>
                        <h4 style="text-transform: uppercase;">PROGRAM STUDI {{ $mahasiswa->department->nama ?? 'Tidak
                            tersedia' }}</h4>
                        <p style="margin-top: 3px; margin-bottom: 1px">Jl. Sultan Hasanuddin No. 234, Unaaha; Website:
                            https://pbi.fkip-uilaki.ac.id</p>
                    </td>
                    <td class="logo1">
                        <img src="data:image/png;base64,{{ $logoUri2 ?? '' }}" alt="Logo Tut Wuri">
                    </td>
                </tr>
            </table>
        </div>
        <div class="hadir" style="text-align: center">
            <h3 class="text-center">
                <strong><u>KARTU HASIL STUDI (KHS)</u></strong>
            </h3>
        </div><br><br>

        <div class="info-section">
            <div class="photo-container" style="float: right">
                <img src="{{ $photo_base64 }}" alt="{{ $mahasiswa->nama_mhs }}"
                    style="width: 85px; height: 90px; border: 1px solid #ccc; margin-top: -.3rem;">

            </div>
            <div class="info-container">
                <table class="info-table">
                    <style>
                        td {
                            padding-top: 5px;
                            padding-bottom: 5px
                        }
                    </style>
                    <tr>
                        <td style="width: 60px">Nama</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->nama_mhs ?? 'Tidak tersedia' }}</td>
                        <td class="tengah"></td>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->department->nama ?? 'Tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->nim ?? 'Tidak tersedia' }}</td>
                        <td class="tengah"></td>
                        <td>Dosen PA</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->advisor->nama_dosen ?? 'Tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->kelas->name ?? 'Tidak tersedia' }}</td>
                        <td class="tengah"></td>
                        <td>Tahun Akademik</td>
                        <td>:</td>
                        <td>{{ $ta->ta }}/{{ $ta->semester }}</td>
                    </tr>
                </table>
            </div>

        </div><br><br>

        <table class="table">
            <tr style="text-align:center">
                <th rowspan="2">No</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Mata Kuliah</th>
                <th rowspan="2">SKS</th>
                <th colspan="4">Nilai</th>
            </tr>
            <tr style="text-align:center">
                <th>Nilai Akhir</th>
                <th>Huruf</th>
                <th>Bobot</th>
                <th>SKS x Bobot</th>
            </tr>
            <tbody>
                @forelse ($grades as $index => $nilai)
                <tr style="text-align:center">
                    <td>{{ $index + 1 }}.</td>
                    <td>{{ $nilai->schedule->course->code ?? 'Tidak tersedia' }}</td>
                    <td style="text-align:left">{{ $nilai->schedule->course->name ?? 'Tidak tersedia' }}</td>
                    <td>{{ $nilai->schedule->course->sks ?? 0 }}</td>
                    <td>{{ $nilai->total ?? 'N/A' }}</td>
                    <td>{{ $nilai->nhuruf ?? 'N/A' }}</td>
                    <td>
                        @if($nilai->nhuruf == "A")
                        {{ 4 }}
                        @elseif($nilai->nhuruf == "B")
                        {{ 3 }}
                        @elseif($nilai->nhuruf == "C")
                        {{ 2 }}
                        @elseif($nilai->nhuruf == "D")
                        {{ 1 }}
                        @else
                        {{ 0 }}
                        @endif
                    </td>
                    <td>
                        @if($nilai->nhuruf == "A")
                        {{ 4 * ($nilai->schedule->course->sks ?? 0) }}
                        @elseif($nilai->nhuruf == "B")
                        {{ 3 * ($nilai->schedule->course->sks ?? 0) }}
                        @elseif($nilai->nhuruf == "C")
                        {{ 2 * ($nilai->schedule->course->sks ?? 0) }}
                        @elseif($nilai->nhuruf == "D")
                        {{ 1 * ($nilai->schedule->course->sks ?? 0) }}
                        @else
                        {{ 0 }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="text-center">
                    <td colspan="8" class="text-danger">Data Mahasiswa belum tersedia.</td>
                </tr>
                @endforelse
                <tr>
                    <td colspan="3" style="text-align: right">Total</td>
                    <td style="text-align: center">{{ $totalSks }}</td>
                    <td colspan="2"></td>
                    <td></td>
                    <td style="text-align: center">{{ $totalBobot }}</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: right">IPK Semester</td>
                    <td style="text-align: center">{{ $totalSks > 0 ? number_format($totalBobot / $totalSks, 2) : 'Belum
                        Ada Data' }}</td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: right; margin: 0; padding: 0">Unaaha, {{ $tgl }}</p><br>
        <table class="signature-table" style="margin: 0; padding: 0">
            <tr>
                <td>
                    <p style="margin: 0 0 0 75px">Mengetahui,<br>Ketua Program Studi<br><br><br><br><u><strong>{{
                                $mahasiswa->department->kaprodi ?? 'Tidak tersedia' }}</strong></u><br>NIDN: {{
                        $mahasiswa->department->nip ?? 'Tidak tersedia' }}</p>
                </td>
                <td style="padding: 0 65px"></td>
                <td>
                    <p style="margin: 0 0 0 65px"><br>Penasehat Akademik<br><br><br><br><u><strong>{{
                                $mahasiswa->advisor->nama_dosen ?? 'Tidak tersedia' }}</strong></u><br>NIDN: {{
                        $mahasiswa->advisor->nidn ?? 'Tidak tersedia' }}</p>
                </td>
            </tr>
        </table>

        <br>
        <table class="table" style="margin-top: 150px">
            <tr>
                <td colspan="2">KHS ini adalah resmi dikeluarkan oleh Prodi {{ $mahasiswa->department->nama ?? 'Tidak
                    tersedia' }}.</td>
            </tr>
        </table>
    </div>
</body>

</html>