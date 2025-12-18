<x-main-layout>
    @section('title', 'Tambah Data Kegiatan Penunjang Dosen')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p class="btn btn-danger"><a class="text-white" href="/dosen/penunjang">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form class="form-validate" method="POST" action="{{ route('lecturer.penunjang.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="title">Judul Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" name="title" placeholder="Masukkan judul kegiatan">
                                </div>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="organizer">Penyelenggara</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('organizer') is-invalid @enderror"
                                        value="{{ old('organizer') }}" name="organizer"
                                        placeholder="Masukkan nama penyelenggara">
                                </div>
                                @error('organizer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="level">Tingkat Kegiatan</label>
                                <div class="form-control-wrap">
                                    <select class="form-select @error('level') is-invalid @enderror" name="level">
                                        <option value="">Pilih Tingkat Kegiatan</option>
                                        <option value="Nasional" {{ old('level')=='Nasional' ? 'selected' : '' }}>
                                            Nasional</option>
                                        <option value="Internasional" {{ old('level')=='Internasional' ? 'selected' : ''
                                            }}>Internasional</option>
                                        <option value="Regional" {{ old('level')=='Regional' ? 'selected' : '' }}>
                                            Regional</option>
                                    </select>
                                </div>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="peran">Peran</label>
                                <div class="form-control-wrap">
                                    <select name="peran" id="peran"
                                        class="form-control @error('peran') is-invalid @enderror">
                                        <option value="">-Pilih Peran-</option>
                                        <option value="Peserta">Peserta</option>
                                        <option value="Narasumber">Narasumber</option>
                                        <option value="Panitia">Panitia</option>
                                    </select>
                                </div>
                                @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="date">Tanggal Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        value="{{ old('date') }}" name="date">
                                </div>
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label" for="proof_type">Tipe Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="proof_type" onchange="toggleProofInput()">
                                        <option value="file">Upload File</option>
                                        <option value="url">URL/Link</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12" id="proof_file_input">
                            <div class="form-group">
                                <label class="form-label" for="proof">File Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="file" class="form-control @error('proof') is-invalid @enderror"
                                        name="proof" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Format: PDF, DOC, DOCX (Max: 2MB)</small>
                                    @error('proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12" id="proof_url_input" style="display: none;">
                            <div class="form-group">
                                <label class="form-label" for="proof_url">URL Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="url" class="form-control @error('proof_url') is-invalid @enderror"
                                        name="proof_url" value="{{ old('proof_url') }}"
                                        placeholder="https://example.com/document.pdf">
                                    <small class="text-muted">Masukkan URL lengkap ke dokumen bukti kegiatan</small>
                                    @error('proof_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('lecturer.penunjang.index') }}"
                                    class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleProofInput() {
            const proofType = document.getElementById('proof_type').value;
            const fileInput = document.getElementById('proof_file_input');
            const urlInput = document.getElementById('proof_url_input');

            if (proofType === 'file') {
                fileInput.style.display = 'block';
                urlInput.style.display = 'none';
                document.querySelector('input[name="proof_url"]').value = '';
            } else {
                fileInput.style.display = 'none';
                urlInput.style.display = 'block';
                document.querySelector('input[name="proof"]').value = '';
            }
        }
    </script>
    @endpush
</x-main-layout>