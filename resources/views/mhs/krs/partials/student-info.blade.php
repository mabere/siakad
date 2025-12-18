<div class="card card-bordered mb-3">
    <div class="card-header">
        <h5 class="card-title">Data Mahasiswa</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-label text-muted">Nama Lengkap</label>
                    <div class="form-control-plaintext">{{ $student->nama_mhs }}</div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-label text-muted">Nomor Induk Mahasiswa</label>
                    <div class="form-control-plaintext">{{ $student->nim }}</div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-label text-muted">Program Studi</label>
                    <div class="form-control-plaintext">{{ $student->department->nama }}</div>
                </div>
            </div>
        </div>
    </div>
</div>