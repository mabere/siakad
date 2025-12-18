<x-main-layout>
    @section('title', 'Kalender Akademik')
    @php
    $user = auth()->user();
    $isAdmin = $user->hasRole('admin');
    @endphp

    @if($isAdmin)
    <div class="mb-4">
        <button onclick="openCreateModal()" class="btn btn-primary">Tambah Acara</button>
    </div>
    @endif

    <div id="calendar"></div>

    @if($isAdmin)
    <div id="eventModal" class="modal" style="display:none;position:absolute">
        <div class="modal-content">
            <h2 id="modalTitle">Tambah Acara</h2>
            <form id="eventForm">
                @csrf
                <input type="hidden" id="eventId">
                <div class="form-group">
                    <label for="title">Judul</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" step="1"
                        required>
                </div>
                <div class="form-group">
                    <label for="end_date">Tanggal Selesai</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" step="1" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="url">URL (Opsional)</label>
                    <input type="url" id="url" name="url" class="form-control"
                        placeholder="http://example.com/event-details">
                </div>
                <div class="form-group">
                    <label for="visibility">Visibilitas</label>
                    <select id="visibility" name="visibility" class="form-control" required>
                        <option value="public">Publik</option>
                        <option value="faculty">Fakultas</option>
                        <option value="department">Program Studi</option>
                    </select>
                </div>
                {{-- Tambahkan field Audiens Target --}}
                <div class="form-group">
                    <label for="target_audience">Audiens Target</label>
                    <select id="target_audience" name="target_audience" class="form-control" required>
                        <option value="semua">Semua Pengguna</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
                {{-- Grup Fakultas dan Program Studi hanya untuk Admin --}}
                <div class="form-group" id="facultyGroup" style="display:none;">
                    <label for="faculty_id">Fakultas</label>
                    <select id="faculty_id" name="faculty_id" class="form-control">
                        @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="departmentGroup" style="display:none;">
                    <label for="department_id">Program Studi</label>
                    <select id="department_id" name="department_id" class="form-control">
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="academic_year_id">Tahun Akademik</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                        @foreach($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}">{{ $academicYear->ta }}/{{ $academicYear->semester }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Batal</button>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('assets/js/calendar.js') }}"></script>
    <script>
        window.eventsData = @json($eventsData, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            window.isAdmin = @json($isAdmin);
            window.isDeanOrHead = false; // Tetap false untuk halaman ini
            console.log('User Roles:', @json($user->roles->pluck('name')));
            console.log('Is Admin (for this page):', window.isAdmin);
    </script>
    @endpush

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-main-layout>
