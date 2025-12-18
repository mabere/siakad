<x-main-layout>
    @section('title', 'Tambah Dosen')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Tambah Data Dosen</h4>
            <div class="nk-block-des">
                <p><a href="">Back</a></p>
            </div>
        </div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Tambah Data Dosen</span>
                <form method="POST" class="form-validate" action="{{ route('staff.dosen.store') }}">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nama_dosen">Nama Dosen</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nama_dosen') }}"
                                        name="nama_dosen" placeholder="Nama Dosen" required>
                                </div>
                                @error('nama_dosen') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nidn">NIDN</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nidi') }}" name="nidn"
                                        placeholder="NIDN Dosen">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                        placeholder="Alamat Email">
                                </div>
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <div class="form-control-wrap ">
                                    <div class="form-control-select">
                                        <select class="form-control" name="gender">
                                            <option value="">Pilih</option>
                                            <option value="Laki-Laki">Laki-Laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
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
                                            <span class="input-group-text" id="fv-phone">+62</span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ old('telp') }}" name="telp"
                                            placeholder="Nomor Telepon">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('tpl') }}" name="tpl"
                                        placeholder="Tempat Lahir">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" value="{{ old('tgl') }}" name="tgl"
                                        placeholder="Tanggal Lahir">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control no-resize" name="address"
                                        placeholder="Alamat Lengkap">{{ old('address') }}</textarea>
                                </div>
                                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-md btn-primary">Tambah</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>