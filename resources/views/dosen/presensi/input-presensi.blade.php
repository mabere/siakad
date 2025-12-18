<x-main-layout>
    @section('title', 'Pengisian Presensi')
    <div class="container py-5">
        <x-custom.sweet-alert />
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h4 class="card-title mb-0">Informasi Mata Kuliah</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="150">Mata Kuliah</td>
                                        <td width="20">:</td>
                                        <td>
                                            {{ $jadwal->course_name ?? 'N/A' }}
                                            ({{ $jadwal->course_code ?? 'N/A' }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Program Studi</td>
                                        <td>:</td>
                                        <td>
                                            @if($jadwal->schedulable_type === 'App\Models\Course' &&
                                            $jadwal->course->department)
                                            {{ $jadwal->course->department->nama }}
                                            @else
                                            MKDU / Tidak Berlaku
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SKS</td>
                                        <td>:</td>
                                        <td>
                                            @if($jadwal->schedulable_type === 'App\Models\Course')
                                            {{ $jadwal->course->sks ?? 'N/A' }} SKS
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Semester</td>
                                        <td>:</td>
                                        <td>
                                            @if($jadwal->schedulable_type === 'App\Models\Course')
                                            {{ $jadwal->course->semester_number ?? 'N/A' }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pertemuan</td>
                                        <td>:</td>
                                        <td>Ke-{{ $pertemuan }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Daftar Hadir Mahasiswa</h4>
                        <form
                            action="{{ route('lecturer.attendance.stores', ['id' => $jadwal->id, 'pertemuan' => $pertemuan]) }}"
                            method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="text-center" width="5%">No.</th>
                                            <th width="15%">NIM</th>
                                            <th>Nama</th>
                                            <th class="text-center" width="20%">Status Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($studyPlans as $index => $studyPlan)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $studyPlan->student->nim ?? 'N/A' }}</td>
                                            <td>{{ $studyPlan->student->nama_mhs ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                $currentStatus =
                                                $existingAttendanceDetails[$studyPlan->student_id]->status ?? '';
                                                @endphp
                                                <select name="attendance[{{ $studyPlan->student_id }}]"
                                                    class="form-select @error('attendance.' . $studyPlan->student_id) is-invalid @enderror">
                                                    {{-- <option value="">Pilih Status</option>dosen --}}
                                                    <option value="Hadir" {{ $currentStatus==='Hadir' ? 'selected' : ''
                                                        }}>Hadir</option>
                                                    <option value="Izin" {{ $currentStatus==='Izin' ? 'selected' : ''
                                                        }}>Izin</option>
                                                    <option value="Sakit" {{ $currentStatus==='Sakit' ? 'selected' : ''
                                                        }}>Sakit</option>
                                                    <option value="Tanpa Keterangan" {{
                                                        $currentStatus==='Tanpa Keterangan' ? 'selected' : '' }}>Tanpa
                                                        Keterangan</option>
                                                </select>
                                                @error('attendance.' . $studyPlan->student_id)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <div class="alert alert-warning mb-0">
                                                    Belum ada mahasiswa yang mengambil mata kuliah ini atau KRS belum
                                                    disetujui.
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($studyPlans->isNotEmpty())
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon ni ni-save-fill"></i>
                                    <span>Simpan Presensi</span>
                                </button>
                                <a href="{{ route('lecturer.attendance.show', $jadwal->id) }}" class="btn btn-danger">
                                    <i class="icon ni ni-reply-all me-1"></i>
                                    <span>Kembali</span>
                                </a>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            let totalSelects = document.querySelectorAll('select[name^="attendance"]').length;
            let filledSelects = document.querySelectorAll('select[name^="attendance"] option:checked:not([value=""])').length;

            if (totalSelects > 0 && totalSelects !== filledSelects) {
                if (!confirm('Beberapa status kehadiran belum diisi. Lanjutkan menyimpan?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
    @endpush
</x-main-layout>
