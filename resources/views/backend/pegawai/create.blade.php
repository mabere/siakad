<x-main-layout>
    @section('title', 'Tambah Pegawai')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p><a href="/admin/pegawai/">Back</a></p>
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
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form method="POST" class="form-validate" action="{{ route('admin.pegawai.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nama">Nama Pegawai</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nama') }}" name="nama"
                                        placeholder="Nama Pegawai" required>
                                </div>
                                @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nip">NIP Pegawai</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nidi') }}" name="nip"
                                        placeholder="NIP Pegawai">
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
                                <label class="form-label" for="department_id">Program Studi</label>
                                <div class="form-control-wrap">
                                    <select class="form-control form-select" name="department_id">
                                        @foreach ($department as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('department_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="position">Jabatan</label>
                                <div class="form-control-wrap">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ old('position') }}"
                                            name="position" placeholder="Jabatan">
                                    </div>
                                </div>
                                @error('position') <div class="text-danger">{{ $message }}</div> @enderror
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