<!DOCTYPE html>
<html>

<head>
    <title>Kartu Rencana Studi</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            /* Font yang lebih umum untuk dokumen resmi */
            font-size: 11pt;
        }

        .container {
            padding: 20px;
            /* Tambahkan padding agar konten tidak terlalu mepet tepi */
        }

        .header {
            margin-bottom: 10px;
            border-bottom: 2px solid black;
            padding-bottom: 5px;
            /* Sedikit padding di bawah garis */
        }

        .kop {
            width: 100%;
            border-collapse: collapse;
        }

        .kop td {
            vertical-align: top;
            /* Align top for better layout */
        }

        .kop .logo1 img {
            width: 70px;
            height: auto;
            /* Biarkan tinggi menyesuaikan agar tidak pecah */
            display: block;
            /* Agar img tidak punya whitespace di bawahnya */
            margin: 0 auto;
            /* Tengah gambar jika td cukup lebar */
        }

        .kop .info {
            text-align: center;
        }

        .kop .info span {
            display: block;
            /* Agar setiap span berada di baris baru */
            line-height: 1.2;
            /* Jarak antar baris */
        }

        .kop .info p {
            margin: 1px 0;
            /* Kurangi margin default p */
            font-size: .98rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table td,
        .table th {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
            /* Default text align for td, can be overridden */
            vertical-align: top;
            /* Default vertical align for table cells */
        }

        .table th {
            background-color: #f2f2f2;
            /* Warna latar untuk header tabel */
            text-align: center;
            /* Sesuaikan text align untuk header */
        }

        .datamhs td {
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            /* Ensure no borders */
        }

        .signature-table td {
            padding: 0 5px;
            /* Adjust padding as needed */
            vertical-align: top;
        }

        .signature-table p {
            margin: 0;
            padding: 0;
        }

        .photo-container {
            margin-right: 1rem;
            width: 85px;
            /* Pastikan ukuran container sesuai gambar */
            height: 90px;
            border: 1px solid #ccc;
            position: absolute;
            z-index: 10;
            /* Sesuaikan margin-top agar foto tidak menimpa teks */
            margin-top: -30px;
            /* Example adjustment, test this value */
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Memastikan gambar mengisi area tanpa terdistorsi */
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="kop">
                <tr>
                    <td class="logo1" style="width: 15%; text-align: center;">
                        <img src="data:image/png;base64,{{ $logoUri ?? '' }}" alt="Logo Unilaki">
                    </td>
                    <td class="info" style="width: 70%;">
                        <span style="font-size:1.5rem"><strong>{{ $yayasan }}</strong></span>
                        <span style="font-size:1.3rem"><strong>{{ $universitas }}</strong></span>
                        <span style="font-size:1.28rem;text-transform: uppercase;"><strong>FAKULTAS {{
                                $mahasiswa->department->faculty->nama }}</strong></span>
                        <span style="font-size:1.16rem;text-transform: uppercase;"><strong>PROGRAM STUDI {{
                                $mahasiswa->department->nama }}</strong></span>
                        <p style="margin-bottom: 1px;margin-top:0;font-size:.98rem">{{ $alamat }}; Website: {{
                            $mahasiswa->department->web }}</p>
                    </td>
                    <td class="logo1" style="width: 15%; text-align: center;">
                        <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Tut Wuri">
                    </td>
                </tr>
            </table>
        </div>

        <div class="hadir" style="text-align: center; margin-bottom: 1.5rem;">
            <h3><u>KARTU RENCANA STUDI (KRS)</u><br>Tahun Akademik: {{ $ta->ta }}/{{ $ta->semester }}</h3>
        </div>

        <table style="width: 100%; margin-bottom: 20px;">
            <tr class="datamhs">
                <td rowspan="3" style="width: 100px; padding-bottom: 0;">
                    <div class="photo-container">
                        <img src="data:image/png;base64,{{ $photo_base64 }}" alt="{{ $mahasiswa->nama_mhs }}">
                    </div>
                </td>
                <td style="width: 100px;">Nama</td>
                <td style="width: 10px;">:</td>
                <td style="width: auto;">{{ $mahasiswa->nama_mhs }}</td>
                <td style="width: 20px;"></td>
                <td style="width: 100px;">Fakultas</td>
                <td style="width: 10px;">:</td>
                <td style="width: auto;">{{ $mahasiswa->department->faculty->nama }}</td>
            </tr>
            <tr class="datamhs">
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mahasiswa->nim }}</td>
                <td></td>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $mahasiswa->department->nama }}</td>
            </tr>
            <tr>
                <td>Angkatan</td>
                <td>:</td>
                <td>{{ $mahasiswa->kelas->angkatan ?? '-' }}/{{ $mahasiswa->kelas->name ?? '-' }}</td>
                <td></td>
                <td>Dosen PA</td>
                <td>:</td>
                <td>{{ $mahasiswa->advisor->nama_dosen ?? ($mahasiswa->kelas->lecturer->nama_dosen ?? '-') }}</td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 15%; text-align: center;">Kode MK</th>
                    <th style="width: 35%; text-align: left;">Mata Kuliah</th>
                    <th style="width: 10%; text-align: center;">SKS</th>
                    <th style="width: 25%; text-align: left;">Dosen Pengampu</th>
                    <th style="width: 10%; text-align: center;">Ket.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $index => $item)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td style="text-align: center">{{ $item->schedule->course->code ?? $item->mkduCourse->code ?? '-' }}
                    </td>
                    <td style="text-align: left">{{ $item->schedule->course->name ?? $item->mkduCourse->name ?? '-' }}
                    </td>
                    <td style="text-align: center">{{ $item->schedule->course->sks ?? $item->mkduCourse->sks ?? 0 }}
                    </td>
                    <td style="text-align: left">
                        @if($item->schedule && !empty($item->schedule->lecturersInSchedule))
                        {{ $item->schedule->lecturersInSchedule->first()->nama_dosen ?? '-' }}
                        @elseif($item->mkduCourse)
                        - @else
                        -
                        @endif
                    </td>
                    <td style="text-align: center">{{ $item->schedule->kelas->name ?? 'MKDU' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Mata Kuliah tidak ada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-weight: bold; margin-bottom: 20px;">
            <span>Total SKS yang Diambil: {{ $totalSks }}</span><br>
            <span>SKS Maksimal yang Dapat Diambil: 24</span>
        </div>

        <p style="text-align: right; margin:0; padding:0;">Unaaha, {{ $tgl }}</p>

        <table class="signature-table">
            <tr>
                <td style="width: 33%;">
                    <p>Menyetujui,<br>Dosen PA</p><br><br><br>
                    <p><u><strong>{{ $mahasiswa->advisor->nama_dosen ?? ($mahasiswa->kelas->lecturer->nama_dosen ?? '-')
                                }}</strong></u><br>NIDN: {{ $mahasiswa->advisor->nidn ??
                        ($mahasiswa->kelas->lecturer->nidn ?? '-') }}</p>
                </td>
                <td style="width: 34%;"></td>
                <td style="width: 33%; text-align: right;">
                    <p><br>Mahasiswa,</p><br><br><br>
                    <p><u><strong>{{ $mahasiswa->nama_mhs }}</strong></u><br>NIM: {{ $mahasiswa->nim }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">
                    <p>Mengetahui,<br>Ketua Program Studi</p><br><br><br>
                    <p><u><strong>{{ $mahasiswa->department->kaprodi ?? '-' }}</strong></u><br>NIDN: {{
                        $mahasiswa->department->nip ?? '-' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 20px;">
                    <p>Catatan :</p>
                    <ul>
                        <li>1 Lembar untuk Mahasiswa</li>
                        <li>1 Lembar untuk Akademik</li>
                        <li>Simpan KRS ini sebaik mungkin di tempat yang aman</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
