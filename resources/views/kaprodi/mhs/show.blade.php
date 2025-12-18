<x-main-layout>
    @section('title', 'Detail Mahasiswa')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail Mahasiswa - {{ $mahasiswa->nama_mhs }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('kaprodi.mahasiswa.index') }}" class="btn btn-success pull-left">
                            <i class="fa fa-arrow-left"></i> Kembali ke Daftar Mahasiswa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <!-- Informasi Pribadi Mahasiswa -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Informasi Pribadi</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3"><strong>NIM:</strong> <span class="text-secondary">{{
                                            $mahasiswa->nim }}</span></p>
                                    <p class="mb-3"><strong>Nama:</strong> <span class="text-secondary">{{
                                            $mahasiswa->nama_mhs }}</span></p>
                                    <p class="mb-3"><strong>Email:</strong> <span class="text-secondary">{{
                                            $mahasiswa->email }}</span></p>
                                    <p class="mb-3"><strong>Telepon:</strong> <span class="text-secondary">{{
                                            $mahasiswa->telp }}</span></p>
                                    <p class="mb-3"><strong>Kelas:</strong> <span class="text-secondary">{{
                                            $mahasiswa->kelas->name ?? 'Belum Ada Kelas' }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">Informasi Akademik</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3"><strong>Program Studi:</strong> <span class="text-secondary">{{
                                            $mahasiswa->department->nama }}</span></p>
                                    <p class="mb-3"><strong>Tahun Masuk:</strong> <span class="text-secondary">{{
                                            $mahasiswa->entry_year }}</span></p>
                                    <p class="mb-3"><strong>Total SKS:</strong> <span class="text-secondary">{{
                                            $mahasiswa->total_sks }}</span></p>
                                    <p class="mb-3"><strong>Pembimbing Akademik:</strong> <span
                                            class="text-secondary">{{ $mahasiswa->advisor->nama_dosen ?? 'Belum
                                            Ditugaskan' }}</span></p>
                                    <p class="mb-3"><strong>Alamat:</strong> <span class="text-secondary">{{
                                            $mahasiswa->address }}</span></p>
                                    <p class="mb-3"><strong>Tanggal Lahir:</strong> <span class="text-secondary">{{
                                            $mahasiswa->tgl ? $mahasiswa->tgl->format('d F Y') : '-' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>