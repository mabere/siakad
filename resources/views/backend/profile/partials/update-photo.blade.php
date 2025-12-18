<form action="{{ $photoRoute }}" method="POST" enctype="multipart/form-data" class="form-validate">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label class="form-label" for="photo">Pilih Foto</label>
        <div class="form-control-wrap">
            <div class="form-file">
                <input type="file" class="form-file-input @error('photo') is-invalid @enderror" id="photo" name="photo"
                    accept="image/*" required>
                <label class="form-file-label" for="photo">Pilih file</label>
            </div>
            @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-note mt-2">
            <ul class="list-inline">
                <li>Format: JPG, JPEG, PNG</li>
                <li>Maksimal: 100Kb</li>
                <li>Lokasi: /images/{{ $folder }}/</li>
            </ul>
        </div>
    </div>
    <x-custom.sweet-alert />
    <div class="form-group preview-image" style="display: none;">
        <label class="form-label">Preview</label>
        <div class="form-control-wrap">
            <img id="photoPreview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; object-fit: cover;"
                class="rounded">
        </div>
    </div>

    <div class="mt-2" id="existing-image-container">
        <img src="{{ asset('storage/images/' . $folder . '/' . $photo) }}" alt="Foto Saat Ini" class="img-thumbnail"
            style="max-width: 200px;">
        <p class="text-muted">Foto saat ini</p>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <em class="icon ni ni-upload-cloud"></em>
            <span>Update Foto</span>
        </button>
    </div>
</form>
