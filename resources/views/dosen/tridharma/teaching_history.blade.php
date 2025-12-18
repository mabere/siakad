<x-main-layout>
    @section('title', 'Riwayat Mengajar Dosen')
    <div class="bg-primary">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title py-2 ps-3 text-white">@yield('title')</h5>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <h6>Riwayat Mengajar Dosen: {{ auth()->user()->lecturer->nama_dosen }}</h6>
                            <form method="GET" action="{{ route('lecturer.riwayat.mengajar') }}" class="mb-4">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="academic_year_id" class="form-label">Tahun Akademik</label>
                                        <select name="academic_year_id" id="academic_year_id" class="form-select">
                                            <option value="">Pilih Tahun Akademik</option>
                                            @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $selectedTa==$year->id ? 'selected' : ''
                                                }}>
                                                {{ $year->ta }} ({{ $year->semester }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select name="semester" id="semester" class="form-select">
                                            <option value="">Pilih Semester</option>
                                            <option value="Ganjil" {{ $selectedSemester=='Ganjil' ? 'selected' : '' }}>
                                                Ganjil</option>
                                            <option value="Genap" {{ $selectedSemester=='Genap' ? 'selected' : '' }}>
                                                Genap</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('lecturer.riwayat.mengajar') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>

                            @if ($data->isEmpty())
                            <div class="alert alert-info">
                                Tidak ada jadwal mengajar untuk filter yang dipilih.
                            </div>
                            @else
                            @foreach ($data->groupBy('academic_year_id') as $year => $schedules)
                            <div class="mb-4">
                                <h6 class="mb-2">Tahun Akademik: {{ $schedules->first()->academicYear->ta }}</h6>

                                @foreach ($schedules->groupBy(function ($item) {return $item->academicYear->semester;})
                                as $semester => $jadwal)
                                <h6 class="mb-2">Semester: {{ $semester }}</h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-secondary text-white">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tahun/Semester</th>
                                                <th>Mata Kuliah</th>
                                                <th>Program Studi</th>
                                                <th>Kelas</th>
                                                <th>SKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jadwal as $index => $schedule)
                                            @php
                                            $course = $schedule->schedulable;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}.</td>
                                                <td>{{ $schedule->academicYear->ta }}/{{
                                                    $schedule->academicYear->semester }}</td>
                                                <td>{{ $course->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($course instanceof \App\Models\Course && $course->department)
                                                    {{ $course->department->nama ?? 'N/A' }}
                                                    @else
                                                    {{ 'MKDU' }}
                                                    @endif
                                                </td>
                                                <td>{{ $schedule->kelas->name ?? 'N/A' }}</td>
                                                <td>{{ $course->sks ?? 'N/A' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
