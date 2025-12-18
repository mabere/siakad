<x-main-layout>
    @section('title', 'Edit Data Kegiatan Penunjang')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form class="form-validate" method="POST"
                    action="{{ route('lecturer.penunjang.update', $penunjang->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="title">Judul Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $penunjang->title) }}" name="title"
                                        placeholder="Masukkan judul kegiatan">
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="organizer">Penyelenggara</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('organizer') is-invalid @enderror"
                                        value="{{ old('organizer', $penunjang->organizer) }}" name="organizer"
                                        placeholder="Masukkan nama penyelenggara">
                                    @error('organizer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="level">Tingkat Kegiatan</label>
                                <div class="form-control-wrap">
                                    <select class="form-select @error('level') is-invalid @enderror" name="level">
                                        <option value="">Pilih Tingkat Kegiatan</option>
                                        @foreach(['Nasional', 'Internasional', 'Regional'] as $level)
                                        <option value="{{ $level }}" {{ old('level', $penunjang->level) == $level ?
                                            'selected' : '' }}>
                                            {{ $level }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="peran">Peran</label>
                                <div class="form-control-wrap">
                                    <select class="form-select @error('peran') is-invalid @enderror" name="peran">
                                        <option value="">Pilih Peran</option>
                                        <option value="Peserta" {{ old('peran', $penunjang->peran) == 'Peserta' ?
                                            'selected' : '' }}>Peserta</option>
                                        <option value="Narasumber" {{ old('peran', $penunjang->peran) ==
                                            'Narasumber' ? 'selected' : '' }}>Narasumber</option>
                                        <option value="Panitia" {{ old('peran', $penunjang->peran) == 'Panitia' ?
                                            'selected' : '' }}>Panitia</option>
                                    </select>
                                    @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="date">Tanggal Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        value="{{ old('date', \Carbon\Carbon::parse($penunjang->date)->format('Y-m-d')) }}"
                                        name="date">
                                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="proof_type">Tipe Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="proof_type" onchange="toggleProofInput()">
                                        <option value="file" {{ !filter_var($penunjang->proof, FILTER_VALIDATE_URL) ?
                                            'selected' : '' }}>Upload File</option>
                                        <option value="url" {{ filter_var($penunjang->proof, FILTER_VALIDATE_URL) ?
                                            'selected' : '' }}>URL/Link</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12" id="proof_file_input"
                            style="{{ !filter_var($penunjang->proof, FILTER_VALIDATE_URL) ? 'display:block' : 'display:none' }}">
                            <div class="form-group">
                                <label class="form-label" for="proof">File Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="file" class="form-control @error('proof') is-invalid @enderror"
                                        name="proof" accept=".pdf,.doc,.docx">
                                    @if($penunjang->proof && !filter_var($penunjang->proof, FILTER_VALIDATE_URL))
                                    <div class="mt-2">
                                        <small class="text-muted">File saat ini: {{ $penunjang->proof }}</small>
                                        <a href="/uploads/penunjang/{{ $penunjang->proof }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary ms-2">
                                            <em class="icon ni ni-eye"></em> Lihat File
                                        </a>
                                    </div>
                                    @endif
                                    <small class="text-muted d-block mt-1">Format: PDF, DOC, DOCX (Max: 2MB)</small>
                                    @error('proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12" id="proof_url_input"
                            style="{{ filter_var($penunjang->proof, FILTER_VALIDATE_URL) ? 'display:block' : 'display:none' }}">
                            <div class="form-group">
                                <label class="form-label" for="proof_url">URL Bukti Kegiatan</label>
                                <div class="form-control-wrap">
                                    <input type="url" class="form-control @error('proof_url') is-invalid @enderror"
                                        name="proof_url"
                                        value="{{ old('proof_url', filter_var($penunjang->proof, FILTER_VALIDATE_URL) ? $penunjang->proof : '') }}"
                                        placeholder="https://example.com/document.pdf">
                                    <small class="text-muted">Masukkan URL lengkap ke dokumen bukti kegiatan</small>
                                    @error('proof_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
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