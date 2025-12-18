<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan EDOM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1,
        h2,
        h3 {
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 5px;
        }

        h1 {
            font-size: 18px;
            text-align: center;
        }

        h2 {
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-top: 20px;
        }

        h3 {
            font-size: 14px;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 style="margin-bottom:0;padding:0">Laporan Evaluasi Dosen oleh Mahasiswa (EDOM)</h1>
        <h2 style="margin-bottom:0"><strong>Fakultas:</strong> {{ $department->faculty->nama ?? 'N/A' }}</h2>
        <p><strong>Program Studi:</strong> {{ $department->nama ?? 'N/A' }}</p>
        <p><strong>Kaprodi:</strong> {{ $kaprodiName ?? 'N/A' }}</p>
        <p><strong>Tahun Akademik/Semester:</strong> {{ $data->first()->academicYear->ta ?? 'N/A' }}/{{
            ucfirst($data->first()->academicYear->semester ?? 'N/A') }}</p>
    </div>

    @foreach ($data as $schedule)
    <h2>{{ $schedule->schedulable->code ?? 'N/A' }} - {{ $schedule->schedulable->name ?? 'N/A' }}</h2>

    <h3>Informasi Jadwal</h3>
    <table>
        <tr>
            <th>Dosen Pengampu</th>
            <th>Jumlah Responden</th>
        </tr>
        <tr>
            <td>
                <ol>
                    @foreach ($schedule->lecturersInSchedule as $lecturer)
                    <li>{{ $lecturer->nama_dosen }}@if(!$loop->last)@endif</li>
                    @endforeach

                </ol>
            </td>
            <td class="text-center">{{ $schedule->responses->count() }}</td>
        </tr>
    </table>

    @php
    $averageRatings = [];
    if ($schedule->responses->isNotEmpty()) {
    $totalResponses = $schedule->responses->count();
    $ratingsByCategory = $schedule->responses->groupBy('question.categoryName.value');
    foreach ($ratingsByCategory as $category => $responses) {
    $averageRatings[$category] = $responses->avg('rating');
    }
    }
    @endphp

    @if (!empty($averageRatings))
    <h3>Rata-rata Penilaian per Kategori</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-center">Rata-rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($averageRatings as $category => $average)
            <tr>
                <td>{{ $category }}</td>
                <td class="text-center">{{ number_format($average, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @php
    $comments = $schedule->responses->whereNotNull('comment');
    @endphp

    @if ($comments->isNotEmpty())
    <h3>Komentar Mahasiswa</h3>
    <ul>
        @foreach ($comments as $response)
        <li>"{{ $response->comment }}"</li>
        @endforeach
    </ul>
    @endif

    @if (!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach

</body>

</html>
