<x-main-layout>
    @section('title', 'Edit Pengumuman')
    <div class="nk-content">
        <div class="card">
            <div class="card-inner">
                <h4>Edit Pengumuman</h4>
                <form action="{{ route('announcements.update', $announcement) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label>Judul</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title',$announcement->title) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>Kategori</label>
                            <input type="text" name="category" class="form-control"
                                value="{{ old('category',$announcement->category) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label>Fakultas</label>
                            <select name="faculty_id" class="form-select" {{
                                in_array(auth()->user()->activeRole(),['dekan','kaprodi','staff'])?'disabled':'' }}
                                required>
                                @foreach($faculties as $f)
                                <option value="{{ $f->id }}" {{ $announcement->faculty_id==$f->id?'selected':'' }}>{{
                                    $f->nama }}</option>
                                @endforeach
                            </select>
                            @if(in_array(auth()->user()->activeRole(),['dekan','kaprodi','staff']))
                            <input type="hidden" name="faculty_id" value="{{ $announcement->faculty_id }}">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Departemen</label>
                            <select name="department_id" class="form-select" {{
                                in_array(auth()->user()->activeRole(),['kaprodi','staff'])?'disabled':'' }} required>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ $announcement->department_id==$d->id?'selected':'' }}>{{
                                    $d->nama }}</option>
                                @endforeach
                            </select>
                            @if(in_array(auth()->user()->activeRole(),['kaprodi','staff']))
                            <input type="hidden" name="department_id" value="{{ $announcement->department_id }}">
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label>Kelas (multi)</label>
                            <select name="kelas_id" class="form-select">
                                @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $announcement->kelas_id ?? null ==
                                    $k->id ? 'selected' : '' }}>

                                    {{ $k->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Target Role</label>
                            <select name="target_role" class="form-select" required>
                                <option value="semua" {{ $announcement->target_role=='semua'?'selected':'' }}>Semua
                                </option>
                                <option value="dosen" {{ $announcement->target_role=='dosen'?'selected':'' }}>Dosen
                                </option>
                                <option value="mahasiswa" {{ $announcement->target_role=='mahasiswa'?'selected':''
                                    }}>Mahasiswa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Status Aktif</label>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" {{
                                    $announcement->is_active?'checked':'' }}>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label>Isi</label>
                            <textarea name="content" class="form-control" rows="5"
                                required>{{ old('content',$announcement->content) }}</textarea>
                        </div>
                        <div class="col-12">
                            <a class="btn btn-warning" href="{{ route('announcements.index') }}"><em
                                    class="icon ni ni-reply"></em></a>
                            <button class="btn btn-success">Perbarui</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
