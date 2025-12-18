<!DOCTYPE html>
<html>

<head>
    <title>Surat Keputusan Ujian Skripsi</title>
    <x-cetakpdf.style />
    <style>
        body,
        * {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .page {
            page-break-after: always;
            padding: 20mm;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .header-kop {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Styling tabel tetap sama seperti sebelumnya */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
    </style>
</head>

<body>
    @php
    $univ = 'Univesitas Lakidende Unaaha';
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

    $judulUjian = $firstExam->exam_type ?? 'Ujian Skripsi';
    $judulLengkap = ucwords(str_replace('_', ' ', $judulUjian));
    $tanggalSekarang = \Carbon\Carbon::now()->isoFormat('D MMMM Y');
    @endphp

    {{-- Halaman 1: Undangan Ujian --}}
    <div class="page">
        <div class="header-kop">
            {{-- Menggunakan Komponen Blade --}}
            <x-kop.kop-surat :logoUri="$logoUri" :logoUri2="$logoUri2" :yayasan="$yayasan" :universitas="$universitas"
                :fakultasNama="$fakultasNama" :fakultasWebsite="$fakultasWebsite" :fakultasEmail="$fakultasEmail"
                :alamat="$alamat" :kab="$kab" :telp="$telp" />
        </div>
        <div class="content">
            <p>Nomor : 073/FKIP-01/KM/VII/2025</p>
            <p>Hal : Undangan Ujian {{ $judulLengkap }}</p>
            <p>Kepada Yth.<br>Bapak/Ibu Dosen<br>di tempat</p>
            <p>Dengan hormat,<br>Dalam rangka penyelesaian Tugas Akhir Penelitian Mahasiswa, Kami mengundang Bapak/Ibu
                Dosen untuk menghadiri/menguji mahasiswa Program Studi {{ $prodi->nama }}, {{ $fakultasNama }} pada
                Seminar {{ $judulLengkap }} yang akan dilaksanakan pada:</p>
            <table style="border: none;">
                <tr>
                    <td style="width: 20%; border: none;">Hari, Tanggal</td>
                    <td style="width: 2%; border: none;">:</td>
                    <td style="border: none;">{{ optional($firstExam->scheduled_at)->isoFormat('dddd, D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Pukul</td>
                    <td style="border: none;">:</td>
                    <td style="border: none;">{{ optional($firstExam->scheduled_at)->format('H:i') }} WITA</td>
                </tr>
                <tr>
                    <td style="border: none;">Tempat</td>
                    <td style="border: none;">:</td>
                    <td style="border: none;">{{ $firstExam->location ?? '-' }}</td>
                </tr>
            </table>
            <p>Demikian undangan ini kami sampaikan, atas kehadira Bapak/Ibu Dosen diucapkan terima kasih.</p>
            <br>
            <div style="text-align: right;">
                <p>Unaaha, {{ $tanggalSekarang }}<br>Dekan FKIP,</p>
                <br><br><br>
                <p><b>{{ $dekan->nama_dosen }}</b><br>NIDN: {{ $dekan->nidn }}</p>
            </div>
        </div>
    </div>

    {{-- Halaman 2: Surat Keputusan --}}
    <div class="page">
        <div class="header-kop">
            {{-- Menggunakan Komponen Blade lagi --}}
            <x-kop.kop-surat :logoUri="$logoUri" :logoUri2="$logoUri2" :yayasan="$yayasan" :universitas="$universitas"
                :fakultasNama="$fakultasNama" :fakultasWebsite="$fakultasWebsite" :fakultasEmail="$fakultasEmail"
                :alamat="$alamat" :kab="$kab" :telp="$telp" />
        </div>
        <div class="content">
            <div>
                <table style="border-collapse: collapse; width: 100%;">
                    <tr style="border: none;">
                        <th colspan="3" style="text-align: center; border: none;">
                            SURAT KEPUTUSAN<br>
                            {{ mb_strtoupper($fakultasNama) }}<br>
                            NOMOR<br><br>
                            TENTANG<br><br>
                            PANITIA SEMINAR HASIL MAHASISWA PROGRAM S-1<br>
                            {{ mb_strtoupper($prodi->nama) }}<br>
                            {{ mb_strtoupper($fakultasNama) }}<br><br>
                            DEKAN {{ mb_strtoupper($fakultasNama) }}
                        </th>
                    </tr>
                    <tr style="border: none;text-align:justify">
                        <td style="border: none;">Menimbang</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;">
                            <ol type="a">
                                <li>Bahwa dalam rangka penyelesain studi Mahasiswa {{ $prodi->nama }},
                                    {{ $fakultasNama }}, {{ $univ }} perlu diadakan Seminar Hasil Penelitian.</li>
                                <li>Bahwa untuk tertibnya pelaksanaan Seminar Hasil tersebut perlu dibentuk suatu
                                    Panitia Seminar hasil Penelitian.</li>
                                <li>Bahwa sehubungan dengan poin a dan b, perlu ditetapkan dengan Surat Keputusan</li>
                            </ol>
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;">Mengingat</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;text-align:justify">
                            <ol type="1">
                                <li>Undang-Undang Nomor 23 tahun 2009 tentang Sistem Pendidikan Nasional.</li>
                                <li>Undang-Undang Nomor 12 Tahun 2012 tentang Pendidikan Tinggi.</li>
                                <li>Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi
                                    dan
                                    Pengelolaan Perguruan Tinggi.</li>
                                <li>Permendikbud Nomor 1 Tahun 2020 tentang Kebijakan Merdeka Belajar dalam penentuan
                                    kelulusan peserta didik dan pelaksanaan penerimaan peserta didik baru tahun ajaran
                                    2023/2024.</li>
                                <li>Keputusan Menristekdikti Nomor 299/KPT/2017 tentang Yayasan Lakidende Razak Porosi
                                    Unaaha sebagai pengelola {{ $univ }}.</li>
                            </ol>
                        </td>
                    </tr>
                    <tr style="border: none;text-align:left;">
                        <td style="border: none;">Memperhatikan</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;">
                            <ol type="1">
                                <li>Statuta {{ $univ }}.</li>
                                <li>Pedoman Akademik {{$fakultasNama}} {{ $univ }}.
                                </li>
                                <li>Usulan permohonan Seminar Hasil mahasiswa.</li>
                            </ol>
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <th colspan="3" style="text-align: center; border: none;">MEMUTUSKAN</th>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;">Menetapkan</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;"></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;">Pertama</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;text-align:justify">
                            Membentuk Panitia Seminar Hasil Mahasiswa {{ $prodi->nama }},
                            {{$fakultasNama}}, {{ ucfirst($univ) }} tahun 2025 sebagaimana tersebut
                            pada lampiran 1 (satu).
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;">Kedua</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;text-align:justify">
                            Menyetujui Peserta Seminar hasil sebagaimana tersebut pada lampiran 2 (dua).<br>
                            Ketiga: Biaya pelaksanaan Seminar Hasil ini dibebankan kepada Mahasiswa serta sumber lain
                            yang
                            dianggap perlu.
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;">Ketiga</td>
                        <td style="border: none;">:</td>
                        <td style="border: none;text-align:justify">
                            Keputusan ini mulai berlaku sejak tanggal ditetapkan dengan ketentuan bahwa apabila di
                            kemudian hari terdapat kekeliruan akan diadakan perbaikan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>

            </div>
            <div style="text-align: left;margin-left:57%">
                <table>
                    <tr>
                        <td style="border: none;margin: 0;width: 30%">Ditetapkan di<br>Pada tanggal</td>
                        <td style="border: none;margin: 0;width: 2%">:<br>:</td>
                        <td style="border: none;margin: 0;;width: 30%">Unaaha<br>{{ $tanggalSekarang }}</td>
                    </tr>
                    <tr>
                        <th style="border: none;margin: 0" colspan="3">Dekan FKIP,<br><br><br><br><u>
                                {{ $dekan->nama_dosen }}</u></b><br>NIDN: {{ $dekan->nidn }}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Halaman 3: Panitia Ujian --}}
    <div class="page">
        <div class="header-kop">
            {{-- Menggunakan Komponen Blade lagi --}}
            <x-kop.kop-surat :logoUri="$logoUri" :logoUri2="$logoUri2" :yayasan="$yayasan" :universitas="$universitas"
                :fakultasNama="$fakultasNama" :fakultasWebsite="$fakultasWebsite" :fakultasEmail="$fakultasEmail"
                :alamat="$alamat" :kab="$kab" :telp="$telp" />
        </div>
        <div class="content">
            <h2 style="text-align: center;">Daftar Panitia Ujian {{ $judulLengkap }}</h2>
            <table class="data">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Pembimbing sebagai Panitia --}}
                    @foreach($firstExam->thesis->supervisions as $j => $sup)
                    <tr>
                        <td>{{ $j + 1 }}.</td>
                        <td>{{ $sup->supervisor->nama_dosen }}</td>
                        <td>Pembimbing {{ $j + 1 }}</td>
                        <td></td>
                    </tr>
                    @endforeach

                    {{-- Penguji sebagai Panitia --}}
                    @foreach($firstExam->examiners as $j => $examiner)
                    @php
                    $jabatan = '';
                    if ($j === 0) {
                    $jabatan = 'Ketua';
                    } elseif ($j === 1) {
                    $jabatan = 'Sekretaris';
                    } else {
                    $jabatan = 'Anggota';
                    }
                    @endphp
                    <tr>
                        <td>{{ count($firstExam->thesis->supervisions) + $j + 1 }}.</td>
                        <td>{{ $examiner->lecturer->nama_dosen }}</td>
                        <td>Penguji {{ $j + 1 }} ({{ $jabatan }})</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="content">
            <br>
            <div style="text-align: left;margin-left:75%">
                <p>Unaaha, {{ $tanggalSekarang }}<br>Dekan FKIP,</p>
                <br><br><br>
                <p><b>{{ $dekan->nama_dosen }}</b><br>NIDN: {{ $dekan->nidn }}</p>
            </div>
        </div>
    </div>

    {{-- Halaman 4: Daftar Skripsi --}}
    <div class="page">
        <div class="header-kop">
            {{-- Menggunakan Komponen Blade lagi --}}
            <x-kop.kop-surat :logoUri="$logoUri" :logoUri2="$logoUri2" :yayasan="$yayasan" :universitas="$universitas"
                :fakultasNama="$fakultasNama" :fakultasWebsite="$fakultasWebsite" :fakultasEmail="$fakultasEmail"
                :alamat="$alamat" :kab="$kab" :telp="$telp" />
        </div>
        <div style="text-align: left">
            <p style="text-align: left">LAMPIRAN II: Surat Keputusan Dekan {{ $fakultas->nama }}</p>
            <p style="text-align: left">NOMOR: 073/FKIP-01/KM/VII/2025</p>
            <p style="text-align: left">TENTANG: Susunan Penguji Seminar {{ $judulLengkap }} Mahasiswa Program Studi {{
                $prodi->nama
                }}
            </p><br>
        </div>

        <div class="content">
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
                <div style="text-align: left;margin-left:75%">
                    <p>Unaaha, {{ date('d M Y') }}</p>
                    <p style="margin-bottom: 4rem;">Dekan {{ $fakultasAcronym }},</p>
                    <p><u>{{ $fakultas->dekanUser->name }}</u></p>
                    <p>NIDN: {{ $fakultas->dekanUser->lecturer->nidn }}</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>