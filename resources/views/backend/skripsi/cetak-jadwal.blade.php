<!DOCTYPE html>
<html>

<head>
    <title>Jadwal Ujian Skripsi</title>
    <x-cetakpdf.style />
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        h2 {
            text-align: center;
        }

        table.cop>th,
        table.cop>td {
            border: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="header"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        @php
        $firstExam = $exams->first();
        $prodi = optional($firstExam->thesis->student->department);
        $fakultas = optional($prodi->faculty);

        $namaFakultas = $fakultas->nama ?? 'N/A';
        $fakultasAcronym = 'N/A';

        if ($fakultas) {
        $words = explode(' ', $fakultas->nama);
        $acronym = '';

        $excludedWords = ['dan', 'di', 'ke', 'untuk'];

        foreach ($words as $word) {
        $lowerWord = strtolower($word);
        if (in_array($lowerWord, $excludedWords)) {
        continue;
        }
        if (!empty($word)) {
        $acronym .= strtoupper(substr($word, 0, 1));
        }
        }
        $fakultasAcronym = $acronym;
        }

        // Ambil exam_type dari data pertama untuk dijadikan judul dinamis
        $examTypeFromDb = $firstExam->exam_type ?? 'Ujian Skripsi';
        $judulLengkap = ucwords(str_replace('_', ' ', $examTypeFromDb));
        @endphp

        <table class="kop" style="width: 100%;">
            <tr>
                <td class="logo1" style="width: 14%; text-align: left; border: 0;">
                    <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="80%" />
                </td>
                <td class="info" style="width: 70%; text-align: center; border: 0;">
                    <p style="font-size: 2rem;margin-bottom:.6rem"><span><b>{{ $yayasan }}</b></span></p>
                    <p style="font-size: 1.8rem;margin-bottom:.51rem"><span><b>{{ $universitas }}</b></span></p>
                    <p style="font-size: 1.5rem;margin-bottom:-.2rem;text-transform: uppercase;"><span><b>{{
                                $fakultas->nama ?? 'N/A'
                                }}</b></span></p>
                    <p style="font-size: .9rem;margin-bottom:-.3rem;"><span>Website: {{ $fakultas->website }}, Telp: {{
                            $telp }} | Email: {{ $fakultas->email }}</span>
                    </p>
                    <p style="font-size: .9rem;"><span>{{ $alamat }}, {{ $kab }}</span></p>
                </td>
                <td class="logo2" style="width: 14%; text-align: right; border: 0;">
                    <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="80%" />
                </td>
            </tr>
        </table>
        <hr style="border-bottom: 2px solid black;">
    </div>

    <div style="text-align: left">
        <p style="text-align: left">LAMPIRAN II: Surat Keputusan Dekan {{ $fakultas->nama }}</p>
        <p style="text-align: left">NOMOR: 073/FKIP-01/KM/VII/2025</p>
        <p style="text-align: left">TENTANG: Susunan Penguji Seminar {{ $judulLengkap }} Mahasiswa Program Studi {{
            $prodi->nama
            }}
        </p><br>
    </div>
    <h2>Daftar Jadwal Ujian {{ $judulLengkap }}</h2>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Mahasiswa</th>
                <th>Pembimbing</th>
                <th>Penguji</th>
                <th>Judul</th>
                <th>Waktu</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $i => $exam)
            <tr>
                <td>{{ $i + 1 }}.</td>
                <td>{{ $exam->thesis->student->nim }}</td>
                <td>{{ $exam->thesis->student->nama_mhs }}</td>
                <td>
                    <ol class="mb-0 ps-3" style="margin: 0; padding-left: 18px;">
                        @foreach($exam->thesis->supervisions as $sup)
                        <li>{{ $sup->supervisor->nama_dosen }}</li>
                        @endforeach
                    </ol>
                </td>
                <td>
                    <ol class="mb-0 ps-3" style="margin: 0; padding-left: 18px;">
                        @foreach($exam->examiners as $examiner)
                        <li>{{ $examiner->lecturer->nama_dosen }}</li>
                        @endforeach
                    </ol>
                </td>
                <td>{{ $exam->thesis->title }}</td>
                <td>{{ $exam->scheduled_at
                    ? \Carbon\Carbon::parse($exam->scheduled_at)->format('d M Y, H:i')
                    : 'Belum Dijadwalkan' }}</td>
                <td>{{ $exam->location ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <div>
        <p>Unaaha, {{ date('d M Y') }}</p>
        <p style="margin-bottom: 4rem;">Dekan {{ $fakultasAcronym }},</p>
        <p><u>{{ $fakultas->dekanUser->name }}</u></p>
        <p>NIDN: {{ $fakultas->dekanUser->lecturer->nidn }}</p>
    </div>
</body>

</html>