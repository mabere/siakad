<x-main-layout>
    @section('title', 'Edit Mahasiswa')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Edit Data Mahasiswa</h4>
            <div class="nk-block-des">
                <p><a href="{{ route('admin.mhs.by.department', $student->department_id) }}">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Update Data Mahasiswa</span>
                <form method="POST" action="{{ route('admin.mhs.update', $student->id) }}" enctype="multipart/form-data"
                    class="form-validate">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nama_mhs">Nama Mahasiswa</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="nama_mhs"
                                        value="{{ old('nama_mhs', $student->nama_mhs) }}">
                                </div>
                                @error('nama_mhs') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nim">NIM</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="nim"
                                        value="{{ old('nim', $student->nim) }}">
                                </div>
                                @error('nim') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="department_id">Program Studi</label>
                                <div class="form-control-wrap">
                                    <input type="hidden" name="department_id" value="{{ $student->department_id }}">
                                    <input type="text" class="form-control" value="{{ $student->department->nama }}"
                                        disabled>
                                </div>
                                @error('department_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" name="email"
                                        value="{{ old('email', $student->email) }}">
                                </div>
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="gender">
                                        <option value="">Pilih</option>
                                        <option value="Laki-Laki" {{ old('gender', $student->gender) == 'Laki-Laki' ?
                                            'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ?
                                            'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="telp">Nomor Telepon</label>
                                <div class="form-control-wrap">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+62</span>
                                        </div>
                                        <input type="text" class="form-control" name="telp"
                                            value="{{ old('telp', $student->telp) }}">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="tpl"
                                        value="{{ old('tpl', $student->tpl) }}">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" name="tgl" id="tgl"
                                        value="{{ old('tgl', $student->tgl ? \Carbon\Carbon::parse($student->tgl)->format('Y-m-d') : '') }}">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control no-resize" style="height: 100px"
                                        name="address">{{ old('address', $student->address) }}</textarea>
                                </div>
                                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>