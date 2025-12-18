<!DOCTYPE html>
<html>

<head>
    <title>Presensi dan BAP</title>
    <x-cetakpdf.style />
</head>

<body>
    <div class="container">
        <x-kop.prodi :prodi="$jadwal->course->department->nama" :fakultas="$jadwal->course->department->faculty->nama"
            :alamat="$jadwal->course->department->alamat" :nomorTelepon="$jadwal->course->department->telp"
            :website="$jadwal->course->department->website" :email="$jadwal->course->department->email"
            :logoUri="$logoUri" :logoUri2="$logoUri2" />

        <h3 style="text-align:center;line-height:1.6rem"><u>DAFTAR HADIR PERKULIAHAN</u><br>ANGKATAN: {{
            $jadwal->kelas->angkatan }}</h3>

        <x-cetakpdf.content-presensi :items="$items" :ta="$ta" :signatures="$signatures" />

        <x-cetakpdf.signature :signatures="$signatures" />

    </div>
</body>

</html>
