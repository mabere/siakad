<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($isEdit) @method('PUT') @endif
    <div class="form-group">
        <label>Nama Gedung</label>
        <input type="text" name="nama" class="form-control" value="{{ old('nama', $building->nama ?? '') }}">
        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="form-group">
        <label>Lokasi</label>
        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $building->lokasi ?? '') }}">
        @error('lokasi') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Tambah' }}</button>
    </div>
</form>
