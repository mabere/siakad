<div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Pengajuan Surat</h5>
                <a href="#" class="close" data-bs-dismiss="modal">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="{{ route('admin.letter-requests.process', $request) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="approve">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Setujui</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>