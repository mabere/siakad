<x-main-layout>
    @section('title', 'Edit Jadwal Kuliah')

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <h5>Terjadi Kesalahan:</h5>
                    <ul class="mb-0">
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

                @if (session('error'))
                <div class="alert alert-warning">
                    {{ session('error') }}
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <form
                    action="{{ route('admin.list-jadwal.update', ['department' => $department->id, 'schedule' => $schedule->id]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="academic_year_id" value="{{ $ta->id }}">

                    <div class="row gy-4">

                        <div class="form-group">
                            <label class="form-label">Tipe Mata Kuliah</label>
                            <div class="form-control-wrap">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="is_mkdu_prodi" name="is_mkdu" value="0"
                                        class="custom-control-input" {{ (old('is_mkdu') ?? (isset($schedule) &&
                                        $schedule->schedulable_type == \App\Models\Course::class
                                    ? 0 : 1) ?? 0) == 0 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_mkdu_prodi">Mata Kuliah Prodi</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="is_mkdu_mkdu" name="is_mkdu" value="1"
                                        class="custom-control-input" {{ (old('is_mkdu') ?? (isset($schedule) &&
                                        $schedule->schedulable_type ==
                                    \App\Models\MkduCourse::class ? 1 : 0) ?? 0) == 1 ? 'checked' : '' }}>
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
                                    @if(isset($schedule) && $schedule->schedulable_type ==
                                    \App\Models\MkduCourse::class)
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

                        {{-- Kelas --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <select class="form-control form-select" name="kelas_id" id="kelas_id">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}" {{ (old('kelas_id') ?? $schedule->kelas_id) ==
                                        $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('kelas_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Ruangan --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Ruangan</label>
                                <select name="room_id" class="form-control form-select">
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach ($rooms as $item)
                                    <option value="{{ $item->id }}" {{ (old('room_id') ?? $schedule->room_id) ==
                                        $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} - {{ $item->nomor }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('room_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Hari --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Hari</label>
                                <select name="hari" class="form-control form-select">
                                    <option value="">-- Pilih Hari --</option>
                                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                    <option value="{{ $day }}" {{ (old('hari') ?? $schedule->hari) == $day ? 'selected'
                                        : '' }}>
                                        {{ $day }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('hari') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="start_time">Mulai</label>
                                <input type="time" name="start_time" class="form-control"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}">
                                @error('start_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="end_time">Selesai</label>
                                <input type="time" name="end_time" class="form-control"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}">
                                @error('end_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        @foreach ([1, 2] as $i)
                        @php
                        $lecturer_id = old("lecturer{$i}_id") ?? ${"lecturer{$i}_id"};
                        $start = old("lecturer{$i}_start") ?? ${"lecturer{$i}_start"};
                        $end = old("lecturer{$i}_end") ?? ${"lecturer{$i}_end"};
                        @endphp
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
                                <input type="number" name="lecturer{{ $i }}_end" id="lecturer{{ $i }}_end" min="1"
                                    max="16" class="form-control" value="{{ old(" lecturer{$i}_end") ??
                                    ${"lecturer{$i}_end"} }}" readonly>
                                @error("lecturer{{ $i }}_end") <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endforeach

                        {{-- Tombol --}}
                        <div class="col-12 text-end">
                            <div class="form-group mt-3">
                                <a href="{{ route('admin.list-jadwal.show', $department->id) }}"
                                    class="btn btn-danger">Batal</a>
                                <button type="submit" class="btn btn-primary">Perbarui Jadwal</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function () {
        // Inisialisasi Select2
        $('#course_id, #mkdu_course_id, #kelas_id, [name="room_id"], [name="hari"]').select2({ // Inisialisasi semua select2 di sini
            placeholder: "-- Pilih --",
            allowClear: true // Izinkan clear selection
        });

        // Pastikan Anda mendapatkan nilai dari radio button yang *terpilih*
        const isMkduRadios = $('input[name="is_mkdu"]'); // Dapatkan semua radio button dengan nama is_mkdu
        const courseProdiGroup = $('#course_prodi_group');
        const courseMkduGroup = $('#course_mkdu_group');
        const courseSelect = $('#course_id');
        const mkduCourseSelect = $('#mkdu_course_id');
        const departmentId = '{{ $department->id }}';

        // Fungsi untuk memuat mata kuliah via AJAX
        function loadCourses(type) {
            let selectElement = (type === 'prodi') ? courseSelect : mkduCourseSelect;
            let currentSelectedValue = selectElement.val();

            selectElement.empty().append('<option value="">-- Pilih Mata Kuliah --</option>'); // Kosongkan dan tambahkan opsi default

            // Hanya panggil AJAX jika departmentId tersedia (untuk prodi) atau jika type adalah mkdu
            if (type === 'prodi' && !departmentId) {
                console.warn('Department ID not found for loading Prodi courses.');
                selectElement.trigger('change');
                return;
            }

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
                    // Jika tidak ada nilai yang dipilih dan ini bukan mode edit, pastikan Select2 menampilkan placeholder
                    else if (!selectElement.val() && !@json(isset($schedule))) { // Cek jika tidak ada nilai dan bukan mode edit
                         selectElement.trigger('change');
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

                // Aktifkan course_id, jadikan required
                courseSelect.prop('disabled', false).attr('required', 'required');
                // Nonaktifkan mkdu_course_id, hapus required
                mkduCourseSelect.prop('disabled', true).removeAttr('required');

                mkduCourseSelect.val(null).trigger('change'); // Kosongkan Select2 MKDU

                loadCourses('prodi');
            } else { // Mata Kuliah MKDU
                courseProdiGroup.hide();
                courseMkduGroup.show();

                // Aktifkan mkdu_course_id, jadikan required
                mkduCourseSelect.prop('disabled', false).attr('required', 'required');
                // Nonaktifkan course_id, hapus required
                courseSelect.prop('disabled', true).removeAttr('required');

                courseSelect.val(null).trigger('change'); // Kosongkan Select2 Prodi

                loadCourses('mkdu');
            }
        }

        // Event listener untuk radio button is_mkdu
        isMkduRadios.on('change', toggleCourseFields);

        // Panggil saat halaman dimuat untuk inisialisasi awal (penting untuk mode edit)
        toggleCourseFields();

        // --- Lecturer Autofill (sudah benar, tinggal pastikan dipanggil) ---
        function autoFillMeetingWeeks(lecturerNumber) {
            const lecturerSelect = $(`#lecturer${lecturerNumber}_id`);
            const startInput = $(`#lecturer${lecturerNumber}_start`);
            const endInput = $(`#lecturer${lecturerNumber}_end`);

            if (lecturerSelect.val()) {
                if (lecturerNumber === 1) {
                    startInput.val(1);
                    endInput.val(8);
                } else if (lecturerNumber === 2) {
                    startInput.val(9);
                    endInput.val(16);
                }
            } else {
                startInput.val('');
                endInput.val('');
            }
        }

        $('#lecturer1_id').on('change', function() {
            autoFillMeetingWeeks(1);
        });

        $('#lecturer2_id').on('change', function() {
            autoFillMeetingWeeks(2);
        });

        // Panggil saat halaman dimuat untuk pre-fill pada mode edit atau jika ada old() value
        autoFillMeetingWeeks(1);
        autoFillMeetingWeeks(2);

        // Trigger change event untuk Select2 agar nilai lama terisi jika ada (khusus mode edit)
        @if(isset($schedule))
            $('#lecturer1_id').trigger('change');
            $('#lecturer2_id').trigger('change');
        @endif
    });
    </script>
    @endpush
</x-main-layout>
