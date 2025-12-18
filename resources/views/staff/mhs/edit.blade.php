<x-main-layout>
    @section('title', 'Edit Mahasiswa')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Edit Data Mahasiswa</h4>
            <div class="nk-block-des">
                <p><a href="/staff/mahasiswa">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Update Data Mahasiswa</span>
                <form method="POST" action="{{ isset($mhs) ? route('staff.mahasiswa.update', $mhs->id) : '#' }}"
                    enctype="multipart/form-data" class="form-validate">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nama_mhs">Nama Mahasiswa</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$mhs->nama_mhs}}" name="nama_mhs">
                                </div>
                                @error('nama_mhs') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="nim">NIM</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$mhs->nim}}" name="nim">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="kelas_id">Kelas</label>
                                <div class="form-control-wrap">
                                    <div class="form-control-select">
                                        <select class="form-control" name="kelas_id">
                                            @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" {{ old('kelas_id')==$item->id ? 'selected' :
                                                '' }}>{{ $item->name }}
                                            </option>
                                            @endforeach
                                            <option value="">Pilih Kelas</option>
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
                                    <input type="email" class="form-control" value="{{$mhs->email}}" name="email">
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
                                            <option {{ ($mhs->gender) ? 'selected' : '' }} value="{{
                                                ($mhs->gender)
                                                }}">{{
                                                $mhs->gender }}</option>
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
                                        <input type="text" class="form-control" value="{{$mhs->telp}}" name="telp">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="{{$mhs->tpl}}" name="tpl">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" id="tgl" name="tgl"
                                        value="{{ old('tgl', $mhs->tgl ? $mhs->tgl->format('Y-m-d') : '') }}">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control no-resize"
                                        name="address">{{ $mhs->address }}</textarea>
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
    @push('scripts')
    <script>
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            const existingImageContainer = document.getElementById('existing-image-container');

            if (file) {
                // Validasi tipe file
                if (!file.type.match('image.*')) {
                    alert('Silakan pilih file gambar!');
                    e.target.value = '';
                    return;
                }

                // Tampilkan preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    if (existingImageContainer) {
                        existingImageContainer.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            } else {
                // Sembunyikan preview jika tidak ada file dipilih
                previewContainer.style.display = 'none';
                if (existingImageContainer) {
                    existingImageContainer.style.display = 'block';
                }
            }
        });
    </script>
    @endpush
</x-main-layout>