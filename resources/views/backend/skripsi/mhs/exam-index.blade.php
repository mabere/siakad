<x-main-layout>
    @section('title', 'Informasi Ujian Skripsi')
    <div class="content-page wide-md m-auto">
        <div class="nk-block-head nk-block-head-sm bg-info px-4">
            <div class="nk-block-head-content text-center">
                <h3 class="nk-block-title p-2 text-white">Informasi Pendaftaran Ujian Skripsi</h3>
                <div class="nk-block-des">
                    <h5 class="text-white">Selamat datang di halaman informasi pendaftaran ujian skripsi mahasiswa
                        lingkup Fakultas Keguruan dan Ilmu Pendidikan Universitas Lakidende Unaaha.</h5>
                </div>
            </div>
        </div>

        <div class="nk-block">
            @if (session('success'))
            <div class="alert alert-icon alert-success" role="alert">
                <em class="icon ni ni-check-circle"></em>
                <strong>{{ session('success') }}</strong>
            </div>
            @endif
            <div class="card card-bordered">
                <div class="card-inner card-inner-lg">
                    <div id="accordion-skripsi" class="accordion accordion-s2">
                        <div class="accordion-item">
                            <a href="#" class="accordion-head" data-bs-toggle="collapse"
                                data-bs-target="#skripsi-item-1">
                                <h6 class="title">1. Informasi Umum</h6>
                                <span class="accordion-icon"></span>
                            </a>
                            <div class="accordion-body collapse show" id="skripsi-item-1"
                                data-bs-parent="#accordion-skripsi">
                                <div class="accordion-inner">
                                    <p>Ujian skripsi merupakan tahapan akhir yang wajib ditempuh oleh setiap mahasiswa
                                        program sarjana sebagai salah satu syarat untuk memperoleh gelar kesarjanaan.
                                        Ujian ini bertujuan untuk menilai kemampuan mahasiswa dalam menyusun,
                                        mempertahankan, dan
                                        mengimplementasikan
                                        hasil penelitian secara mandiri.</p>
                                    <h4>Jadwal Pendaftaran:</h4>
                                    <ul class="list list-sm list-checked">
                                        <li>Pembukaan Pendaftaran: 1 Juli 2025</li>
                                        <li>Penutupan Pendaftaran: 30 Agustus 2025</li>
                                        <li>Batas Akhir Pengumpulan Berkas: 15 Agustus 2025</li>
                                    </ul>
                                    <p>Jadwal ujian akan diumumkan setelah proses verifikasi berkas selesai dan akan
                                        dipublikasikan
                                        langsung melalui Aplikasi Siakad.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <a href="#" class="accordion-head collapsed" data-bs-toggle="collapse"
                                data-bs-target="#skripsi-item-2">
                                <h6 class="title">2. Persyaratan Pendaftaran</h6>
                                <span class="accordion-icon"></span>
                            </a>
                            <div class="accordion-body collapse" id="skripsi-item-2"
                                data-bs-parent="#accordion-skripsi">
                                <div class="accordion-inner">
                                    <p>Untuk dapat mendaftar ujian skripsi, mahasiswa wajib memenuhi persyaratan sebagai
                                        berikut:</p>
                                    <h4>2.1. Persyaratan Akademik</h4>
                                    <ul class="list list-sm list-checked">
                                        <li>Penyelesaian Seluruh Mata Kuliah: Mahasiswa telah menyelesaikan seluruh
                                            beban studi
                                            (SKS) mata kuliah wajib dan pilihan, kecuali skripsi, dengan IPK minimal
                                            2.75 dan tanpa
                                            nilai E.</li>
                                        <li>Surat Keterangan Bebas Pustaka: Mahasiswa telah memperoleh surat keterangan
                                            bebas
                                            pinjam buku dari perpustakaan universitas/fakultas.</li>
                                        <li>Surat Keterangan Bebas Administrasi Keuangan: Mahasiswa telah melunasi
                                            seluruh
                                            kewajiban administrasi keuangan dan memperoleh surat keterangan bebas
                                            tunggakan dari
                                            bagian keuangan.</li>
                                        <li>Status Terdaftar: Mahasiswa aktif terdaftar pada semester berjalan.</li>
                                    </ul>
                                    <h4>2.2. Persyaratan Skripsi</h4>
                                    <ul class="list list-sm list-checked">
                                        <li>Skripsi Telah Selesai: Naskah skripsi telah diselesaikan secara keseluruhan,
                                            disetujui oleh dosen pembimbing, dan telah melalui proses bimbingan sesuai
                                            dengan
                                            ketentuan yang berlaku.</li>
                                        <li>Persetujuan Dosen Pembimbing: Mahasiswa wajib melampirkan surat persetujuan
                                            ujian
                                            skripsi dari kedua dosen pembimbing.</li>
                                        <li>Cek Plagiarisme: Naskah skripsi telah melewati pemeriksaan plagiarisme
                                            dengan hasil
                                            maksimal 20% kemiripan, dibuktikan dengan laporan hasil cek plagiarisme.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <a href="#" class="accordion-head collapsed" data-bs-toggle="collapse"
                                data-bs-target="#skripsi-item-3">
                                <h6 class="title">3. Ketentuan Pendaftaran dan Prosedur</h6>
                                <span class="accordion-icon"></span>
                            </a>
                            <div class="accordion-body collapse" id="skripsi-item-3"
                                data-bs-parent="#accordion-skripsi">
                                <div class="accordion-inner">
                                    <p>Pendaftaran ujian skripsi dilakukan secara daring (online) melalui aplikasi
                                        SIAKAD Fakultas
                                        KEguruan dan Ilmu Pendidikan. Berikut adalah langkah-langkah dan ketentuan yang
                                        harus
                                        diikuti:</p>
                                    <ul class="list list-sm list-checked">
                                        <li>Pengisian Formulir Pendaftaran Online: Akses SIAKAD dan isi formulir
                                            pendaftaran ujian
                                            skripsi dengan data yang benar dan lengkap.</li>
                                        <li>Unggah Berkas Persyaratan: Unggah seluruh berkas persyaratan dalam format
                                            PDF ke sistem
                                            informasi akademik. Pastikan setiap berkas diberi nama file yang jelas
                                            (contoh:
                                            Nama_NIM_SuratPersetujuanDosenPembimbing.pdf).</li>
                                        <li>Daftar Berkas yang Diunggah:
                                            <ul class="list list-sm">
                                                <li>Lembar Persetujuan Ujian Skripsi dari Dosen Pembimbing.</li>
                                                <li>Transkrip Nilai.</li>
                                                <li>Kartu Hasil Studi (KHS) semester berjalan.</li>
                                                <li>Surat Keterangan Bebas Pustaka.</li>
                                                <li>Surat Keterangan Bebas Administrasi Keuangan.</li>
                                                <li>Laporan Hasil Cek Plagiarisme Skripsi.</li>
                                                <li>Naskah Skripsi Lengkap dalam format PDF.</li>
                                            </ul>
                                        </li>
                                        <li>Verifikasi Berkas: Setelah mengunggah berkas, tim administrasi akan
                                            melakukan
                                            verifikasi. Mahasiswa akan menerima notifikasi langsung melalui SIAKAD
                                            mengenai status
                                            verifikasi
                                            (diterima/ditolak beserta alasannya).</li>
                                        <li>Pembayaran Biaya Ujian. Bukti pembayaran wajib diunggah ke sistem.</li>
                                        <li>Penerbitan Jadwal Ujian: Mahasiswa yang berkasnya telah diverifikasi dan
                                            disetujui
                                            akan diinformasikan mengenai jadwal ujian skripsi yang meliputi tanggal,
                                            waktu,
                                            ruang ujian, dan nama tim penguji.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    <hr class="mt-5 mb-4">
                    <div class="entry text-center">
                        <div class="mt-6 text-center">
                            <p class="lead">PENDAFTARAN UJIAN</p>
                            <a href="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}"
                                class="btn btn-primary">DAFTAR</a>
                            @if($latestExam)
                            <a href="{{ route('mahasiswa.thesis.exam.show', [$thesis->id, $latestExam->id]) }}"
                                class="btn btn-info">Lihat
                                Detail Pendaftaran</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
