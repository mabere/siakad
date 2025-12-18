<x-main-layout>
    @section('title', 'Buat Pengumuman')
    <div class="nk-content">
        <div class="card">
            <div class="card-inner">
                <h4>Buat Pengumuman Baru</h4>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label>Judul</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Kategori</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Fakultas</label>
                            <select name="faculty_id" class="form-select" {{
                                in_array(auth()->user()->activeRole(),['dekan','kaprodi','staff'])?'disabled':'' }}>
                                @foreach($faculties as $f)
                                <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                @endforeach

                                @foreach($faculties as $f)
                                <option value="{{ $f->id }}" {{ old('faculty_id', $faculties->first()->id) == $f->id ?
                                    'selected' : '' }}>
                                    {{ $f->nama }}
                                </option>
                                @endforeach

                            </select>
                            @if(in_array(auth()->user()->activeRole(),['dekan','kaprodi','staff']))
                            <input type="hidden" name="faculty_id" value="{{ $faculties->first()->id }}">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Program Studi (Opsional)<small class="text-danger">*</small></label>
                            <select name="department_id" class="form-select" {{
                                in_array(auth()->user()->activeRole(),['kaprodi','staff'])?'disabled':'' }}>
                                <option value="">Pilih Prodi</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>

                            @if(in_array(auth()->user()->activeRole(),['kaprodi','staff']))
                            <input type="hidden" name="department_id" value="{{ $departments->first()->id }}">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Pilih Kelas (Opsional)<small class="text-danger">*</small></label>
                            <select name="kelas_id" class="form-select">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $announcement->kelas_id ?? null) ==
                                    $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Target Role</label>
                            <select name="target_role" class="form-select" required>
                                <option value="semua">Semua</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Status Aktif</label>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" checked>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label>Isi</label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <a class="btn btn-warning" href="{{ route('announcements.index') }}"><em
                                    class="icon ni ni-reply"></em></a>
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="m-3"><small class="text-danger p-2">*Pilih bila diperuntukkan untuk Prodi/Kelas tertentu</small>
            </div>
        </div>
    </div>

</x-main-layout>