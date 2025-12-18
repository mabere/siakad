<script>
    $(document).ready(function() {
    $('#editBapModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var pertemuan = button.data('pertemuan');
        var topik = button.data('topik');
        var keterangan = button.data('keterangan');
        var id = button.data('id');

        // Tampilkan data dalam alert untuk verifikasi
        alert("Pertemuan: " + pertemuan + "\nTopik: " + topik + "\nKeterangan: " + keterangan);

        var modal = $(this);
        modal.find('#bapId').val(id);
        modal.find('#topik').val(topik);
        modal.find('#keterangan').val(keterangan);

        // Tambahkan log untuk memeriksa nilai setelah form diisi
        console.log("Nilai setelah diisi:");
        console.log("ID:", modal.find('#bapId').val());
        console.log("Topik:", modal.find('#topik').val());
        console.log("Keterangan:", modal.find('#keterangan').val());
    });
});

</script>