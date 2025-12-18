<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SURAT PERMOHONAN CUTI MAHASISWA</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            display: flex;
            text-align: center;
            border-bottom: 2px solid black;
            margin-bottom: 10px;
        }

        .header img {
            width: 100px;
            display: flex;
            height: 100px;
        }

        .info {
            text-align: center;
        }

        .table {
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px;
            border: 1px solid #000;
        }

        .logo1 {
            margin: 50px
        }

        .content p {
            line-height: 1.5;
            margin-bottom: 1rem;
            text-align: justify;
        }

        .content-table {
            margin: 1.5rem 0 1.5rem 2rem;
        }

        .content-table td {
            padding: 5px 10px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    @php
    $level = $letterRequest->letterType->level ?? 'university';
    $department = $letterRequest->user->student->department;
    $faculty = $department->faculty;
    @endphp
    @if($level === 'university')
    <x-letterhead.university />
    @elseif($level === 'faculty')
    <x-letterhead.faculty :faculty="$faculty" />
    @elseif($level === 'department')
    <x-letterhead.department :department="$department" />
    @endif

    <!-- Judul Surat -->
    <div style="text-align: center; margin-bottom: 20px;">
        <p><u><strong>SURAT PERMOHONAN CUTI MAHASISWA</strong></u><br>
            Nomor: {{ $reference_number }}</p>
    </div>

    <!-- Konten Surat -->
    <div class="content" style="margin: 0 3rem 4rem 3rem">
        <p style="text-align: justify;space-between:10px">Dekan Fakultas {{
            $letterRequest->user->student->department->faculty->nama }},
            Universitas Lakidende Unaaha, dengan ini menerima permohonan cuti dari:</p>

        <div style="margin: 20px 0 20px 40px;">
            <table>
                <tr>
                    <td style="width: 120px;">Nama</td>
                    <td>: {{ $form_data['Nama'] }}</td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>: {{ $form_data['Nim'] }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: {{ $form_data['Prodi'] }}</td>
                </tr>
            </table>
        </div>

        <p style="text-align: justify;space-between:10px">Dengan rincian permohonan cuti sebagai berikut:</p>

        <div style="margin: 20px 0 20px 40px;">
            <table>
                <tr>
                    <td style="width: 120px;">Alasan Cuti</td>
                    <td>: {{ $form_data['Alasan'] }}</td>
                </tr>
                <tr>
                    <td>Periode Cuti</td>
                    <td>: {{ $form_data['Periode'] }}</td>
                </tr>
                <tr>
                    <td>Tahun Akademik</td>
                    <td>: {{ $academic_year }}</td>
                </tr>
            </table>
        </div>

        <p style="text-align: justify;space-between:10px">Permohonan cuti ini telah disetujui oleh Fakultas {{
            $letterRequest->user->student->department->faculty->nama }}
            dan berlaku sesuai ketentuan yang berlaku di Universitas Lakidende Unaaha.</p>
        <p style="text-align: justify;space-between:10px">Demikian surat ini dikeluarkan untuk dapat digunakan
            sebagaimana mestinya.</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature" style="text-align: left;margin-left: 65%">
        <table>
            <tr>
                <td><span>Unaaha, {{ $date }}</span></td>
            </tr>
            <tr>
                <td><span>Dekan,</span></td>
            </tr>
            <tr style="margin-left: -8px">
                <td><img src="data:image/png;base64,{{ $barcodeImage }}" alt="QR Code" width="125" height="125"></td>
            </tr>
            <tr>
                <td><u><strong>{{ $dean_name }}</strong></u><br>
                    NIP. {{ $dean_nip }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
