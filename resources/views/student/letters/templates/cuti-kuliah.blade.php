<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan Cuti Kuliah</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h3, .header h4 {
            margin: 5px 0;
        }
        .content {
            margin: 20px 0;
        }
        .signature {
            margin-top: 50px;
            float: right;
            text-align: center;
        }
        .reference {
            margin-bottom: 20px;
        }
        .date {
            margin-bottom: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        <h3>UNIVERSITAS CONTOH</h3>
        <h4>FAKULTAS ILMU KOMPUTER</h4>
        <p>Jl. Contoh No. 123, Kota Contoh 12345</p>
        <p>Telp: (021) 123456, Email: info@example.com</p>
        <hr>
    </div>

    <div class="reference">
        <table>
            <tr>
                <td>Nomor</td>
                <td>: {{ $letter->reference_number }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: -</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Permohonan Cuti Kuliah</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table>
            <tr>
                <td width="120">Nama</td>
                <td>: {{ $letter->user->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>: {{ $letter->user->username }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $letter->form_data['program_studi'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $letter->form_data['semester'] ?? '-' }}</td>
            </tr>
        </table>

        <p>Dengan ini mengajukan permohonan cuti kuliah untuk:</p>
        <table>
            <tr>
                <td width="120">Semester</td>
                <td>: {{ $letter->form_data['semester_cuti'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tahun Akademik</td>
                <td>: {{ $letter->form_data['tahun_akademik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alasan Cuti</td>
                <td>: {{ $letter->form_data['alasan_cuti'] ?? '-' }}</td>
            </tr>
        </table>

        <p>Demikian surat permohonan ini saya buat dengan sebenarnya. Atas perhatian Bapak/Ibu, saya ucapkan terima kasih.</p>
    </div>

    <div class="date">
        {{ $letter->created_at->translatedFormat('l, d F Y') }}
    </div>

    <div class="signature">
        <p>Hormat saya,</p>
        <br><br><br>
        <p>{{ $letter->user->name }}</p>
        <p>NIM. {{ $letter->user->username }}</p>
    </div>
</body>
</html>
