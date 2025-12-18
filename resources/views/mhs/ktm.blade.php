<!DOCTYPE html>
<html>

<head>
    <title>Student ID Cards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            .card-container {
                width: 400px;
                height: 250px;
                border: 1px solid #ccc;
                margin-bottom: 20px;
                padding: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
            }

            .card-body {
                display: flex;
                align-items: center;
            }

            .card-photo {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                margin-right: 20px;
            }

            .card-details {
                flex: 1;
            }

            .card-details p {
                margin: 5px 0;
            }

            .card-barcode {
                margin-top: 10px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Student ID Cards</h2>
        @foreach ($students as $student)
        <div class="card-container">
            <div class="card-header uppercase">{{ $student->department->nama }}</div>
            <div class="card-body">
                <img src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo" class="card-photo">
                <div class="card-details">
                    <p><strong>NAME:</strong> {{ $student->nama_mhs }}</p>
                    <p><strong>DATE OF BIRTH:</strong> {{ \Carbon\Carbon::parse($student->tgl)->format('d M Y') }}</p>
                    <p><strong>ADDRESS:</strong> {{ $student->address }}</p>
                    <p><strong>ID SCHOOL:</strong> {{ $student->nim }}</p>
                </div>
            </div>
            <div class="card-barcode">
                {{-- {!! QrCode::size(200)->generate($student->nim) !!} --}}
            </div>
        </div>
        @endforeach
        <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
    </div>
</body>

</html>
