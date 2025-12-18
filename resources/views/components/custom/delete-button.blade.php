<form method="POST" action="{{ $actionUrl }}" style="display: inline;">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i
            class=" icon ni ni-trash"></i></button>

</form>