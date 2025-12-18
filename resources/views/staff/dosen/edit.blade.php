<x-main-layout>
    @section('title', 'Edit Dosen')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p><a href="">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Update Data Dosen</span>
                <form class="form-validate" method="POST" action="{{ route('staff.dosen.update', $dosen->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nama_dosen">Nama Dosen</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="nama_dosen" name="nama_dosen"
                                        value="{{ old('nama_dosen', $dosen->nama_dosen) }}" placeholder="Nama Dosen"
                                        required>
                                </div>
                                @error('nama_dosen') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nidn">NIDN</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="nidn" name="nidn"
                                        value="{{ old('nidn', $dosen->nidn) }}" placeholder="NIDN Dosen" required>
                                </div>
                                @error('nidn') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $dosen->email) }}" placeholder="Alamat Email" required>
                                </div>
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="Laki-Laki" {{ old('gender', $dosen->gender) == 'Laki-Laki' ?
                                            'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ old('gender', $dosen->gender) == 'Perempuan' ?
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
                                        <input type="text" class="form-control" id="telp" name="telp"
                                            value="{{ old('telp', $dosen->telp) }}">
                                    </div>
                                </div>
                                @error('telp') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tpl">Tempat Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="tpl" name="tpl"
                                        value="{{ old('tpl', $dosen->tpl) }}">
                                </div>
                                @error('tpl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="tgl">Tanggal Lahir</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" id="tgl" name="tgl"
                                        value="{{ old('tgl', $dosen->tgl ? $dosen->tgl->format('Y-m-d') : '') }}">
                                </div>
                                @error('tgl') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Alamat</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" id="address"
                                        name="address">{{ old('address', $dosen->address) }}</textarea>
                                </div>
                                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-md btn-primary">Update</button>
                                <a href="{{ route('staff.dosen.index') }}" class="btn btn-md btn-secondary">Cancel</a>
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
