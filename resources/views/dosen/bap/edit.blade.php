<form method="POST" action="{{ route('lecturer.bap.store', ['id' => $jadwal->id, 'pertemuan' => $pertemuan]) }}">
    @csrf
    <div class="mb-3">
        <label for="topik" class="form-label"><i class="icon ni ni-bookmark"></i> Topik</label>
        <input type="text" name="topik" id="topik" class="form-control" value="{{ $bap->topik ?? '' }}" required>
    </div>

    <div class="mb-3">
        <label for="keterangan" class="form-label"><i class="icon ni ni-align-left"></i> Uraian</label>
        <textarea name="keterangan" id="keterangan" class="form-control"
            rows="4">{{ $bap->keterangan ?? '' }}</textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('lecturer.bap.show', $jadwal->id) }}" class="btn btn-outline-danger">
            <i class="icon ni ni-arrow-left"></i> Kembali
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="icon ni ni-save"></i> Simpan
        </button>
    </div>
</form>
