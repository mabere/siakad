<x-main-layout>
    @section('title', 'Tambah Mahasiswa')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">

            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form method="POST" action="{{ route('staff.mahasiswa.store') }}" enctype="multipart/form-data"
                    class="form-validate">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nama_mhs">Nama Mahasiswa</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nama_mhs') }}"
                                        name="nama_mhs" placeholder="Nama Mahasiswa">
                                </div>
                                @error('nama_mhs') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nim">NIM</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('nim') }}" name="nim"
                                        placeholder="NIM Mahasiswa">
                                </div>
                                @error('nim') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <div class="form-control-wrap">
                                    <div class="form-control-select">
                                        <select class="form-control" name="kelas_id">
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" {{ old('kelas_id')==$item->id ? 'selected' :
                                                '' }}>
                                                {{ $item->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('kelas_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" value="{{ old('email') }}" name="email"
                                        placeholder="Alamat Email">
                                </div>
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <div class="form-control-wrap">
                                    <div class="form-control-select">
                                        <select class="form-control" name="gender">
                                            <option value="">Pilih</option>
                                            <option value="Laki-Laki" {{ old('gender')=='Laki-Laki' ? 'selected' : ''
                                                }}>
                                                Laki-Laki</option>
                                            <option value="Perempuan" {{ old('gender')=='Perempuan' ? 'selected' : ''
                                                }}>
                                                Perempuan</option>
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
                                        <input type="text" class="form-control" value="{{ old('telp') }}" name="telp">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{ old('tpl') }}" name="tpl">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" value="{{ old('tgl') }}" name="tgl">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control no-resize"
                                        name="address">{{ old('address') }}</textarea>
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