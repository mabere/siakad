<!-- Modal Konfirmasi Publish -->
<div class="modal fade" id="modalPublish" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form id="publishForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Terbitkan</h5>
                <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menerbitkan kegiatan ini? Semua pengguna yang relevan akan diberi notifikasi.</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info">Terbitkan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>