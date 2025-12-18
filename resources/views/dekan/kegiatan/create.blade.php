<x-main-layout>
    @section('title', 'Buat Kegiatan Baru')

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Buat Kegiatan Akademik Baru</h1>

        <form method="POST" action="{{ route('dekan.kegiatan.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul Kegiatan:</label>
                <input type="text" id="title" name="title"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                @error('title')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Uraian Kegiatan
                    (Opsional):</label>
                <textarea id="description" name="description" rows="4"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                @error('description')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai:</label>
                <input type="datetime-local" id="start_date" name="start_date" step="1"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                @error('start_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai:</label>
                <input type="datetime-local" id="end_date" name="end_date" step="1"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                @error('end_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="url" class="block text-gray-700 text-sm font-bold mb-2">URL Kegiatan (Opsional):</label>
                <input type="url" id="url" name="url"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="http://example.com/event-details">
                @error('url')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="visibility" class="block text-gray-700 text-sm font-bold mb-2">Visibilitas:</label>
                <select id="visibility" name="visibility"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="faculty">Fakultas</option>
                    <option value="department">Program Studi</option>
                </select>
                @error('visibility')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="target_audience" class="block text-gray-700 text-sm font-bold mb-2">Audiens Target:</label>
                <select id="target_audience" name="target_audience"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="semua">Semua Pengguna</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
                @error('target_audience')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4" id="departmentGroup" style="display:none;">
                <label for="department_id" class="block text-gray-700 text-sm font-bold mb-2">Program Studi:</label>
                <select id="department_id" name="department_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Pilih Program Studi</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->nama }}</option>
                    @endforeach
                </select>
                @error('department_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="academic_year_id" class="block text-gray-700 text-sm font-bold mb-2">Tahun Akademik:</label>
                <select id="academic_year_id" name="academic_year_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    @foreach($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->ta }}/{{ $academicYear->semester }}
                    </option>
                    @endforeach
                </select>
                @error('academic_year_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Tambah Kegiatan
                </button>
                <a href="{{ route('dekan.kegiatan.index') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            const departmentGroup = document.getElementById('departmentGroup');
            const departmentIdSelect = document.getElementById('department_id');

            function toggleDepartmentGroup() {
                if (visibilitySelect.value === 'department') {
                    departmentGroup.style.display = 'block';
                    departmentIdSelect.setAttribute('required', 'required');
                } else {
                    departmentGroup.style.display = 'none';
                    departmentIdSelect.removeAttribute('required');
                    departmentIdSelect.value = '';
                }
            }
            toggleDepartmentGroup();
            visibilitySelect.addEventListener('change', toggleDepartmentGroup);
        });
    </script>
    @endpush
</x-main-layout>
