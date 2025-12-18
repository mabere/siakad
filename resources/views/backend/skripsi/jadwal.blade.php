<x-main-layout>
    @section('title', 'Jadwal Ujian Skripsi')

    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">Daftar Jadwal Ujian Skripsi</h4>

            {{-- Form untuk Cetak Jadwal Berdasarkan Tanggal --}}
            <div class="mb-4">
                <form action="{{ route('jadwal.ujian.cetak') }}" method="GET" target="_blank"
                    class="d-flex align-items-end">
                    <div class="me-3">
                        <label for="filter_date" class="form-label">Filter Tanggal Ujian:</label>
                        <input type="date" name="date" id="filter_date" class="form-control"
                            value="{{ request('date') }}">
                    </div>
                    <div class="me-3">
                        <label for="filter_exam_type" class="form-label">Filter Jenis Ujian:</label>
                        <select name="exam_type" id="filter_exam_type" class="form-select">
                            <option value="">Semua Jenis Ujian</option>
                            <option value="proposal" {{ request('exam_type')=='proposal' ? 'selected' : '' }}>Proposal
                            </option>
                            <option value="hasil" {{ request('exam_type')=='hasil' ? 'selected' : '' }}>Hasil</option>
                            <option value="tutup" {{ request('exam_type')=='tutup' ? 'selected' : '' }}>Tutup</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i> Cetak Jadwal
                    </button>
                </form>
            </div>

            <hr>

            {{-- Tabel Jadwal Ujian --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Judul</th>
                        <th>Pembimbing</th>
                        <th>Penguji</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $i => $exam)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $exam->thesis->student->nama_mhs }}</td>
                        <td>{{ $exam->thesis->student->nim }}</td>
                        <td>{{ $exam->thesis->title }}</td>
                        <td>
                            <ol class="mb-0 ps-3" style="margin: 0; padding-left: 18px;">
                                @foreach($exam->thesis->supervisions as $j => $sup)
                                <li>{{ $sup->supervisor->nama_dosen }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>
                            <ol class="mb-0 ps-3" style="margin: 0; padding-left: 18px;">
                                @foreach($exam->examiners as $j => $examiner)
                                <li>{{ $examiner->lecturer->nama_dosen }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>{{ $exam->scheduled_at
                            ? \Carbon\Carbon::parse($exam->scheduled_at)->format('d M Y, H:i')
                            : 'Belum Dijadwalkan' }}</td>
                        <td>{{ $exam->location ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">Tidak ada jadwal ujian yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>