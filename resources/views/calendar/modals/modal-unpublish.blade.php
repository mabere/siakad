<!-- Modal Konfirmasi Unpublish -->
<div class="modal fade" id="modalUnpublish" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form id="unpublishForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Publikasi</h5>
                <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
            </div>
            <div class="modal-body">
                <p>Yakin ingin mengubah status kegiatan menjadi draft kembali?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Ubah ke Draft</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>