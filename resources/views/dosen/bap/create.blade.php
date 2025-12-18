<x-main-layout>
    @section('title', 'Berita Acara Perkuliahan')

    <div class="nk-content">
        <div class="container-xl">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="title text-white"><em class="fas fa-file-alt me-2"></em> Berita Acara
                                    Perkuliahan</h5>
                                <span class="badge bg-light text-primary">Pertemuan Ke-{{ $pertemuan }}</span>
                            </div>
                            <div class="card-inner">
                                <div class="row gy-4">
                                    <div class="col-md-12">
                                        <table class="table table-borderless mb-4">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Mata Kuliah</strong></td>
                                                    <td>:</td>
                                                    <td>
                                                        @if ($jadwal->schedulable_type === 'App\Models\Course')
                                                        {{ $jadwal->schedulable->name ?? 'N/A' }}
                                                        @elseif ($jadwal->schedulable_type === 'App\Models\MkduCourse')
                                                        {{ $jadwal->schedulable->name ?? 'N/A' }}
                                                        @else
                                                        Mata Kuliah Tidak Dikenal
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Dosen</strong></td>
                                                    <td>:</td>
                                                    <td>
                                                        @php
                                                        $authLecturerId = auth()->user()->lecturer->id;
                                                        $currentLecturer = null;
                                                        // Cari dosen yang berhak untuk pertemuan ini
                                                        foreach ($jadwal->lecturersInSchedule as $lecturerItem) {
                                                        if ($pertemuan >= $lecturerItem->pivot->start_pertemuan &&
                                                        $pertemuan <= $lecturerItem->
                                                            pivot->end_pertemuan) {
                                                            $currentLecturer = $lecturerItem;
                                                            break;
                                                            }
                                                            }
                                                            @endphp
                                                            @if ($currentLecturer)
                                                            <span class="badge bg-info text-white">{{
                                                                $currentLecturer->nama_dosen ?? 'N/A'
                                                                }}</span>
                                                            @if ($authLecturerId != $currentLecturer->id)
                                                            <p class="text-danger mt-1">Anda bukan dosen pengampu untuk
                                                                pertemuan ini!</p>
                                                            @endif
                                                            @else
                                                            <span class="text-danger">Tidak ada dosen yang terhubung
                                                                dengan jadwal ini untuk
                                                                pertemuan ini.</span>
                                                            @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Program Studi</strong></td>
                                                    <td>:</td>
                                                    <td>
                                                        @if ($jadwal->schedulable_type === 'App\Models\Course' &&
                                                        $jadwal->schedulable->department)
                                                        {{ $jadwal->schedulable->department->nama ?? 'N/A' }}
                                                        @else
                                                        Mata Kuliah Dasar Umum
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal Isi</strong></td>
                                                    <td>:</td>
                                                    <td><span class="badge bg-secondary">{{ date('d M Y') }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <hr class="mt-4 mb-4">

                                <form method="POST"
                                    action="{{ route('lecturer.bap.store', ['id' => $jadwal->id, 'pertemuan' => $pertemuan]) }}">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="topik" class="form-label"><i class="ni ni-bookmark"></i>
                                            Topik</label>
                                        <input type="text" name="topik" id="topik" class="form-control"
                                            value="{{ $bap->topik ?? '' }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="keterangan" class="form-label"><i class="ni ni-align-left"></i>
                                            Uraian</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control"
                                            rows="4">{{ $bap->keterangan ?? '' }}</textarea>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('lecturer.bap.show', $jadwal->id) }}"
                                            class="btn btn-light border border-danger text-danger">
                                            <em class="ni ni-arrow-left"></em> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <em class="ni ni-save"></em> Simpan
                                        </button>
                                    </div>
                                </form>

                            </div> <!-- .card-inner -->
                        </div> <!-- .card -->
                    </div> <!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

    <x-custom.sweet-alert />
</x-main-layout>
