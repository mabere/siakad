<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan kegiatan ini? Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Batalkan Kegiatan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>