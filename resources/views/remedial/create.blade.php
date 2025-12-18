<x-main-layout>
    @section('title','Form Perbaikan Nilai')

    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-group label {
            font-weight: bold;
        }
    </style>
    <div class="form-container">
        <h2 class="text-center mb-4">Ajukan Remedial Nilai</h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('mhs.remedial.store') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" value="{{ auth()->user()->name }}" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="nim">NIM</label>
                <input type="text" class="form-control" id="nim" value="{{ auth()->user()->student->nim ?? '' }}"
                    readonly>
            </div>

            <div class="form-group mb-3">
                <label for="prodi">Program Studi</label>
                <input type="text" class="form-control" id="prodi"
                    value="{{ auth()->user()->student->department->nama ?? '' }}" readonly>
            </div>


            <div class="form-group mb-3">
                <label for="course_id" class="form-label">Mata Kuliah</label>
                <select name="course_id" id="course_id" class="form-control" required onchange="updateCurrentGrade()">
                    <option value="">Pilih Mata Kuliah</option>
                    @foreach ($courses as $course)
                    <option value="{{ $course->id }}" data-schedule-id="{{ $scheduleMap[$course->id] ?? '' }}"
                        data-grade="{{ $grades[$scheduleMap[$course->id]] ?? 'N/A' }}">
                        {{ $course->code }} - {{ $course->name }} ({{ $course->semester . '/' . $course->smt}})
                    </option>
                    @endforeach
                </select>
                @error('course_id')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="current_grade">Nilai Saat Ini</label>
                <input type="text" class="form-control" id="current_grade" name="current_grade" required>
            </div>

            <div class="form-group mb-3">
                <label for="semester">Semester</label>
                <select class="form-control form-select" id="semester" name="semester" required>
                    <option value="">Pilih Semester</option>
                    @for ($i = 1; $i <= 8; $i++) <option value="{{ $i }}" {{ $i==$activeAcademicYear->semester ?
                        'selected' : '' }}>{{ $activeAcademicYear->ta . '/' . $activeAcademicYear->semester .
                        ' / Semester '
                        . $i
                        }}
                        </option>
                        @endfor
                </select>

            </div>
            <div class="form-group mb-3">
                <label for="document">Dokumen Pendukung</label>
                <input type="file" class="form-control" id="document" name="document" accept=".pdf">
                <small class="form-text text-muted">File harus PDF, maksimum 2MB.</small>
                @error('document')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Ajukan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCurrentGrade() {
                const select = document.getElementById('course_id');
                const selectedOption = select.options[select.selectedIndex];
                const grade = selectedOption.getAttribute('data-grade') || 'N/A';
                document.getElementById('current_grade').value = grade;
            }
    </script>
    <script>
        function updateCurrentGradeAndSemester() {
        const select = document.getElementById('course_id');
        const selectedOption = select.options[select.selectedIndex];

        const grade = selectedOption.getAttribute('data-grade') || 'N/A';
        const semester = selectedOption.getAttribute('data-semester') || '';

        document.getElementById('current_grade').value = grade;
        document.getElementById('semester').value = semester;
    }
    </script>


</x-main-layout>