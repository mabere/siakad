<!-- Modal Edit Mahasiswa -->
<div class="modal fade" id="editMahasiswaModal" tabindex="-1" aria-labelledby="editMahasiswaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editBioModalLabel">Edit Data Diri Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.bio.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role" value="mahasiswa">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_mhs" class="form-control" value="{{ $mahasiswa->nama_mhs }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->gender }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tpl" class="form-control" value="{{ $mahasiswa->tpl }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl" class="form-control"
                                value="{{ $mahasiswa->tgl->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->nim }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->email }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3">{{ $mahasiswa->address }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="modal-footer d-flex justify-content-between">
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
