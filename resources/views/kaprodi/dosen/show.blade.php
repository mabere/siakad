<x-main-layout>
    @section('title', 'Detail Dosen')

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <!-- Card Header dengan Gradien -->
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title py-2 mb-0">@yield('title') - {{ $dosen->nama_dosen }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('kaprodi.dosen.index') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali ke Daftar Dosen
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Alert untuk Status dan Error -->
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Informasi Pribadi Dosen -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-purple">
                                    <h5 class="card-title text-white">Informasi Pribadi</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>NIDN:</strong> {{ $dosen->nidn }}</p>
                                    <p><strong>Nama:</strong> {{ $dosen->nama_dosen }}</p>
                                    <p><strong>Email:</strong> {{ $dosen->email }}</p>
                                    <p><strong>Telepon:</strong> {{ $dosen->telp }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-purple">
                                    <h5 class="card-title text-white">Informasi Akademik</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Program Studi:</strong> {{ $dosen->department->nama }}</p>
                                    <p><strong>Alamat:</strong> {{ $dosen->address }}</p>
                                    <p><strong>Jenis Kelamin:</strong> {{ $dosen->gender }}</p>
                                    <p><strong>Tanggal Lahir:</strong> {{ $dosen->tgl ? date('d-m-Y',
                                        strtotime($dosen->tgl)) : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Mahasiswa yang Dibimbing -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">Mahasiswa Bimbingan Akademik (Dosen PA)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-hover table-striped">
                                            <thead class="bg-gradient-secondary text-white">
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Kelas</th>
                                                    <th>Angkatan</th>
                                                    <th>Total SKS</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($mahasiswa as $index => $mhs)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">{{ $mhs->nim }}</td>
                                                    <td class="text-left">{{ $mhs->nama_mhs }}</td>
                                                    <td>{{ $mhs->kelas->name ?? 'Belum Ada Kelas' }}</td>
                                                    <td class="text-center">{{ $mhs->entry_year }}</td>
                                                    <td class="text-center">{{ $mhs->total_sks }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('kaprodi.mahasiswa.show', $mhs->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fa fa-eye"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-danger py-4">Belum ada
                                                        mahasiswa yang dibimbing</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center">
                                        {{ $mahasiswa->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>