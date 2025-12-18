<!-- Modal Edit Data Dosen -->
<div class="modal fade" id="editBioModal" tabindex="-1" aria-labelledby="editBioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editBioModalLabel">Edit Data Dosen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.bio.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_dosen" class="form-label">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_dosen" id="nama_dosen" class="form-control"
                                value="{{ old('nama_dosen', $fields['Nama Lengkap'] ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIDN</label>
                            <input type="text" class="form-control" value="{{ $dosen->nidn }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tpl" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tpl" id="tpl" class="form-control"
                                value="{{ old('tpl', $fields['Tempat Lahir'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="jafung" class="form-label">Jabatan Fungsional <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="jafung" id="jafung" class="form-control"
                                value="{{ old('jafung', $fields['Jabatan Fungsional'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tgl" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl" id="tgl" class="form-control"
                                value="{{ old('tgl', $dosen->tgl ? $dosen->tgl->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tpl" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telp" id="telp" class="form-control"
                                value="{{ old('telp',  $fields['Telpon'] ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Google Scholar<span class="text-danger">*</span></label>
                            <input type="text" name="scholar_google" class="form-control"
                                value="{{ $dosen->scholar_google }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email<span class="text-danger"></span></label>
                            <input type="text" class="form-control" value="{{ $dosen->email }}" readonly>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Alamat Lengkap <span
                                    class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control"
                                rows="2">{{ old('address', $dosen->address) }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><em class="icon ni ni-save me-1"></em>
                                Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer  d-flex justify-content-between">
                <div class="text-left text-muted">
                    <span class="text-danger">* Kolom wajib diisi. Kolom lain hanya dapat dibaca.</span>
                </div>
            </div>
        </div>
    </div>
</div>
