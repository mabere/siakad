<x-main-layout>
    @section('title', 'Edit Dosen')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Edit Data Dosen</h4>
            <div class="nk-block-des">
                <p><a href="">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Update Data Dosen</span>
                <form class="form-validate" method="POST" action="{{ route('admin.dosen.update', $dosen->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nama_dosen">Nama Dosen</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$dosen->nama_dosen}}"
                                        name="nama_dosen" placeholder="Nama Dosen">
                                </div>
                                @error('nama_dosen') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nidn">NIDN</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$dosen->nidn}}" name="nidn"
                                        placeholder="NIDN Dosen">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="department_id">Program Studi</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="department_id">
                                        <option {{ ($dosen->department_id) ? 'selected' : '' }} value="{{
                                            ($dosen->department_id)
                                            }}">{{
                                            $dosen->department->nama }}</option>
                                        <option value="1">Pend. Bahasa dan Sastra Indonesia</option>
                                        <option value="2">Pend. Bahasa Inggris</option>
                                        <option value="3">Pend. Matematika</option>
                                    </select>
                                </div>
                                @error('department_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" value="{{$dosen->email}}" name="email">
                                </div>
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="scholar_google">Profile Google Scholar</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$dosen->scholar_google}}">
                                </div>
                                @error('scholar_google') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <div class="form-control-wrap ">
                                    <div class="form-control-select">
                                        <select class="form-control" name="gender">
                                            <option {{ ($dosen->gender) ? 'selected' : '' }} value="{{ ($dosen->gender)
                                                }}">{{
                                                $dosen->gender }}</option>
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
                                            <span class="input-group-text" id="telp">+62</span>
                                        </div>
                                        <input type="text" class="form-control" value="{{$dosen->telp}}" name="telp">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$dosen->tpl}}" name="tpl">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" value="{{ $dosen->tgl }}" name="tgl">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control no-resize"
                                        name="address">{{ $dosen->address }}</textarea>
                                </div>
                                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-md btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
