<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        /* Header/Kop Surat */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .header img {
            height: 80px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            margin: 0;
        }

        /* Nomor Surat & Tanggal */
        .letter-info {
            margin-bottom: 30px;
        }

        .letter-info p {
            margin: 5px 0;
        }

        /* Konten Surat */
        .content {
            margin-bottom: 50px;
        }

        /* Tanda Tangan */
        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature p {
            margin: 5px 0;
        }

        .signature-space {
            height: 60px;
            /* Ruang untuk tanda tangan */
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div class="header">
        <img src="{{ $institution['logo'] }}" alt="Logo">
        <h1>{{ $institution['name'] }}</h1>
        <p>{{ $institution['address'] }}</p>
        <p>{{ $institution['phone'] }} | {{ $institution['email'] }}</p>
    </div>

    <!-- Nomor Surat & Informasi -->
    <div class="letter-info">
        <p style="text-align: right">Tanggal: {{ $date }}</p>
        <p>Nomor: {{ $reference_number }}</p>
        <p>Perihal: {{ $letterRequest->letterType->name }}</p>
    </div>

    <!-- Konten Surat -->
    <div class="content">
        @foreach($formData as $key => $value)
        @if(is_string($value))
        <p>{!! nl2br(e($value)) !!}</p>
        @endif
        @endforeach
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <p>{{ $date }}</p>
        <p>{{ $letterRequest->user->name }}</p>
        <div class="signature-space"></div>
        <p><strong>{{ $letterRequest->user->name }}</strong></p>
        <p>NIP/NIK: {{ $letterRequest->user->employee_id ?? '-' }}</p>
    </div>
</body>

</html>