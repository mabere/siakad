<x-main-layout>
    @section('title', 'Daftar Mahasiswa')

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <!-- Card Header dengan Gradien -->
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title py-2 mb-0">Daftar Mahasiswa - {{ $department->nama }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>

                <!-- Daftar Mahasiswa yang Dibimbing -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <!-- Alert untuk Status dan Error -->
                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            @endif
                            @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            @endif

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-hover table-striped">
                                        <thead class="bg-secondary text-white">
                                            <tr>
                                                <th>No.</th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Kelas</th>
                                                <th>Angkatan</th>
                                                <th>Pembimbing Akademik</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mahasiswa as $index => $mhs)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}.</td>
                                                <td class="text-center">{{ $mhs->nim }}</td>
                                                <td class="text-left">{{ $mhs->nama_mhs }}</td>
                                                <td>{{ $mhs->kelas->name ?? 'Belum Ada Kelas' }}</td>
                                                <td class="text-center">{{ $mhs->entry_year }}</td>
                                                <td>{{ $mhs->advisor->nama_dosen ?? 'Belum Ditugaskan' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('kaprodi.mahasiswa.show', $mhs->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-danger text-center">Data mahasiswa tidak
                                                    tersedia</td>
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
</x-main-layout>