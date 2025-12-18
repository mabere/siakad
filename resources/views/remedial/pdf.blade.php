<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Perbaikan Nilai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header img {
            max-width: 100px;
        }

        .content {
            margin-bottom: 40px;
        }

        .footer {
            text-align: center;
        }

        .qr-code {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo">
        <h2>Perbaikan Nilai Akademik</h2>
        <p>Universitas Lakidende</p>
    </div>

    <div class="content">
        <p><strong>ID Pengajuan:</strong> {{ $request->id }}</p>
        <p><strong>Nama Mahasiswa:</strong> {{ $user->name }}</p>
        <p><strong>Mata Kuliah:</strong> {{ $request->course->name ?? 'Mata Kuliah Tidak Ditemukan' }}</p>
        <p><strong>Semester:</strong> {{ $request->semester }}</p>
        <p><strong>Nilai Sebelumnya:</strong> {{ $request->current_grade }}</p>
        <p><strong>Nilai Setelah Remedial:</strong> {{ $request->requested_grade }}</p>
        <p><strong>Tanggal Persetujuan:</strong> {{ now()->format('d F Y') }}</p>
    </div>

    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{{ $barcodeImage }}" alt="QR Code">
    </div>

    <div class="footer">
        <p>Disetujui oleh Kaprodi</p>
        <p>{{ $request->approvedBy->name ?? 'Nama Kaprodi' }}</p>
    </div>
</body>

</html>
