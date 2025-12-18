<!-- Modal Edit Data Diri - Staff -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Data Dosen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.bio.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role" value="staff">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ $staff->nama }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">NIP</label>
                        <input type="text" class="form-control" value="{{ $staff->nip }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $staff->email }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Posisi/Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="position" class="form-control" value="{{ $staff->position }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control"
                            rows="2">{{ old('alamat', $staff->alamat) }}</textarea>

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