x-main-layout<x-main-layout>
    @section('title','Tambah Jadwal Kuliah')

    <div class="card card-bordered card-preview">
        @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Terjadi Kesalahan:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>

            @if ($errors->has('conflict'))
            @foreach ($errors->get('conflict') as $conflictMessage)
            <p class="mb-1">{{ $conflictMessage }}</p>
            @endforeach
            @endif
        </div>
        @endif

        <div class="card-inner">
            <form action="{{ route('admin.list-jadwal.store', $department->id) }}" method="POST">
                @csrf

                <input type="hidden" name="department_id" value="{{ $department->id }}">
                <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">

                <div class="row gy-4">
                    <div class="form-group">
                        <label class="form-label">Tipe Mata Kuliah</label>
                        <div class="form-control-wrap">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="is_mkdu_prodi" name="is_mkdu" value="0"
                                    class="custom-control-input" {{ (old('is_mkdu') ?? (isset($schedule) &&
                                    $schedule->schedulable_type == \App\Models\Course::class ? 0 :
                                1) ?? 0) == 0 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_mkdu_prodi">Mata Kuliah Prodi</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="is_mkdu_mkdu" name="is_mkdu" value="1"
                                    class="custom-control-input" {{ (old('is_mkdu') ?? (isset($schedule) &&
                                    $schedule->schedulable_type == \App\Models\MkduCourse::class ?
                                1 : 0) ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_mkdu_mkdu">Mata Kuliah MKDU</label>
                            </div>
                        </div>
                        @error('is_mkdu') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div id="course_prodi_group"
                        style="{{ (old('is_mkdu') ?? (isset($schedule) && $schedule->schedulable_type == \App\Models\Course::class ? 0 : 1) ?? 0) == 0 ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label class="form-label">Mata Kuliah Prodi</label>
                            <select name="course_id" id="course_id" class="form-control form-select">
                                <option value="">-- Pilih Mata Kuliah Prodi --</option>
                                @if(isset($schedule) && $schedule->schedulable_type == \App\Models\Course::class)
                                <option value="{{ $schedule->schedulable_id }}" selected>
                                    {{ $schedule->schedulable->code }} - {{ $schedule->schedulable->name }}
                                </option>
                                @elseif(old('course_id'))
                                @php $oldCourse = \App\Models\Course::find(old('course_id')); @endphp
                                @if($oldCourse)
                                <option value="{{ $oldCourse->id }}" selected>
                                    {{ $oldCourse->code }} - {{ $oldCourse->name }}
                                </option>
                                @endif
                                @endif
                            </select>
                            @error('course_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div id="course_mkdu_group"
                        style="{{ (old('is_mkdu') ?? (isset($schedule) && $schedule->schedulable_type == \App\Models\MkduCourse::class ? 1 : 0) ?? 0) == 1 ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label class="form-label">Mata Kuliah MKDU</label>
                            <select name="mkdu_course_id" id="mkdu_course_id" class="form-control form-select">
                                <option value="">-- Pilih Mata Kuliah MKDU --</option>
                                @if(isset($schedule) && $schedule->schedulable_type == \App\Models\MkduCourse::class)
                                <option value="{{ $schedule->schedulable_id }}" selected>
                                    {{ $schedule->schedulable->code }} - {{ $schedule->schedulable->name }}
                                </option>
                                @elseif(old('mkdu_course_id'))
                                @php $oldMkduCourse = \App\Models\MkduCourse::find(old('mkdu_course_id')); @endphp
                                @if($oldMkduCourse)
                                <option value="{{ $oldMkduCourse->id }}" selected>
                                    {{ $oldMkduCourse->code }} - {{ $oldMkduCourse->name }}
                                </option>
                                @endif
                                @endif
                            </select>
                            @error('mkdu_course_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-6">
                        <x-form.select name="kelas_id" label="Kelas" :options="$kelas->pluck('name','id')"
                            value="{{ old('kelas_id') }}" />
                    </div>
                    <div class="col-6">
                        <x-form.select name="room_id" label="Ruangan" :options="$rooms->pluck('name','id')"
                            value="{{ old('room_id') }}" />
                    </div>
                    <div class="col-6">
                        <x-form.select name="hari" label="Hari"
                            :options="array_combine(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])"
                            :value="old('hari')" />
                    </div>
                    <div class="col-3">
                        <x-form.input name="start_time" label="Mulai" type="time" value="{{ old('start_time') }}" />
                    </div>
                    <div class="col-3">
                        <x-form.input name="end_time" label="Selesai" type="time" value="{{ old('end_time') }}" />
                    </div>

                    {{-- Dosen 1 & 2 --}}
                    @foreach ([1, 2] as $i)
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">Dosen {{ $i }}</label>
                            <select name="lecturer{{ $i }}_id" class="form-control form-select"
                                id="lecturer{{ $i }}_id">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach ($lecturers as $item)
                                <option value="{{ $item->id }}" {{ (old("lecturer{$i}_id") ??
                                    ${"lecturer{$i}_id"})==$item->id ? 'selected' : '' }}>
                                    {{ $item->nama_dosen }}
                                </option>
                                @endforeach
                            </select>
                            @error("lecturer{$i}_id") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label">Minggu Awal (Otomatis)</label>
                            <input type="number" name="lecturer{{ $i }}_start" id="lecturer{{ $i }}_start" min="1"
                                max="16" class="form-control" value="{{ old(" lecturer{$i}_start") ??
                                ${"lecturer{$i}_start"} }}" readonly>
                            @error("lecturer{$i}_start") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label">Minggu Akhir (Otomatis)</label>
                            <input type="number" name="lecturer{{ $i }}_end" id="lecturer{{ $i }}_end" min="1" max="16"
                                class="form-control" value="{{ old(" lecturer{$i}_end") ?? ${"lecturer{$i}_end"} }}"
                                readonly>
                            @error("lecturer{{ $i }}_end") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @endforeach

                    <div class="col-12 text-end">
                        <a href="{{ route('admin.list-jadwal.show',$department->id) }}" class="btn btn-danger">Batal</a>
                        <button class="btn btn-primary">Simpan Jadwal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#course_id, #mkdu_course_id').select2({
                placeholder: "-- Pilih Mata Kuliah --",
                allowClear: true // Izinkan clear selection
            });

            const courseProdiGroup = $('#course_prodi_group');
            const courseMkduGroup = $('#course_mkdu_group');
            const courseSelect = $('#course_id');
            const mkduCourseSelect = $('#mkdu_course_id');
            const departmentId = '{{ $department->id }}';

            // Fungsi untuk memuat mata kuliah via AJAX
            function loadCourses(type) {
                let selectElement = (type === 'prodi') ? courseSelect : mkduCourseSelect;
                let currentSelectedValue = selectElement.val();

                selectElement.empty().append('<option value="">-- Pilih Mata Kuliah --</option>').trigger('change');

                $.ajax({
                    url: '{{ route('admin.get-courses-by-type') }}',
                    method: 'GET',
                    data: {
                        type: type,
                        department_id: departmentId
                    },
                    success: function(data) {
                        $.each(data, function(key, value) {
                            selectElement.append('<option value="' + value.id + '">' + value.code + ' - ' + value.name + ' (' + value.sks + ' sks)</option>');
                        });
                        // Setel kembali nilai yang sebelumnya dipilih (penting untuk mode edit dan old() values)
                        if (currentSelectedValue) {
                            selectElement.val(currentSelectedValue).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading courses:', error);
                        alert('Gagal memuat daftar mata kuliah.');
                    }
                });
            }

            // Fungsi untuk toggle form mata kuliah dan memuat data
            function toggleCourseFields() {
                const isMkdu = $('input[name="is_mkdu"]:checked').val();

                if (isMkdu === '0') { // Mata Kuliah Prodi
                    courseProdiGroup.show();
                    courseMkduGroup.hide();
                    mkduCourseSelect.val(null).trigger('change'); // Kosongkan MKDU
                    loadCourses('prodi');
                } else { // Mata Kuliah MKDU
                    courseProdiGroup.hide();
                    courseMkduGroup.show();
                    courseSelect.val(null).trigger('change'); // Kosongkan Prodi
                    loadCourses('mkdu');
                }
            }

            // Event listener untuk radio button is_mkdu
            $('input[name="is_mkdu"]').on('change', toggleCourseFields);

            // Panggil saat halaman dimuat untuk inisialisasi awal (penting untuk mode edit)
            toggleCourseFields();

            // ... (kode otomatisasi minggu pertemuan dosen yang sudah ada) ...
            // Pastikan kode autofill dosen ada di sini
            autoFillMeetingWeeks(1);
            autoFillMeetingWeeks(2);
            $('#lecturer1_id').trigger('change');
            $('#lecturer2_id').trigger('change');
        });
    </script>
    @endpush
</x-main-layout>
