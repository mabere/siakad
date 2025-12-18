<!DOCTYPE html>
<html>

<head>
    <title>Laporan BAP</title>
    <x-cetakpdf.style />
</head>

<body>
    <x-kop.prodi :prodi="$jadwal->kelas->department->nama" :fakultas="$jadwal->kelas->department->faculty->nama"
        :alamat="'Jl. Sultan Hasanuddin No. 234, Unaaha'" :nomorTelepon="'(0408) 2421-777'"
        :website="'https://fkip.unilaki.ac.id'" :email="'info@fkip.unilaki.ac.id'" :logoUri="$logoUri"
        :logoUri2="$logoUri2" />

    <x-cetakpdf.presensi-data :jadwal="$jadwal" :ta="$ta" :baps="$baps" :attendances="$attendances"
        :signatures="$signatures" />
    <x-cetakpdf.signature :signatures="$signatures" />
    <div style="page-break-before: always;"></div>

    <x-kop.prodi :prodi="$jadwal->kelas->department->nama" :fakultas="$jadwal->kelas->department->faculty->nama"
        :alamat="'Jl. Sultan Hasanuddin No. 234, Unaaha'" :nomorTelepon="'(0408) 2421-777'"
        :website="'https://fkip.unilaki.ac.id'" :email="'info@fkip.unilaki.ac.id'" :logoUri="$logoUri"
        :logoUri2="$logoUri2" />

    <x-cetakpdf.bap-data :jadwal="$jadwal" :ta="$ta" :baps="$baps" :attendances="$attendances"
        :signatures="$signatures" />
    <x-cetakpdf.signature :signatures="$signatures" style="page-break-before: always;" />
</body>

</html>