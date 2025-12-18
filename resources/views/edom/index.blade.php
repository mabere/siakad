<x-main-layout>
    @if(!$isEdomActive)
    <div class="alert alert-warning alert-icon">
        <em class="icon ni ni-alert-circle"></em>
        EDOM sedang tidak aktif
    </div>
    @endif

    <!-- ... kode yang sudah ada ... -->

    @if($settings['notification_enabled'])
    <!-- Tampilkan notifikasi jika ada -->
    @endif
</x-main-layout>

<script>
    function toggleEdom() {
    fetch('{{ route("admin.edom.settings.toggle") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI sesuai status
            const toggleBtn = document.querySelector('.custom-switch-input');
            toggleBtn.checked = data.is_active;
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error.message || 'Terjadi kesalahan saat mengubah status EDOM'
        });
    });
}
</script>