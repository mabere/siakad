<x-main-layout>
    @section('title', 'Detail Kelas')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data @yield('title') {{ $item->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Nama Kelas:</strong> {{ $item->name }}</p>
                            <p><strong>Program Studi:</strong> {{ $item->department->nama }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Dosen Wali:</strong> {{ $item->lecturer->nama_dosen ?? 'Belum Ditugaskan' }}</p>
                            <p><strong>Tahun Angkatan:</strong> {{ $item->angkatan }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Jumlah Mahasiswa:</strong> {{ $jumlahMahasiswa }}</p>
                        </div>
                    </div><br>

                    <div class="row mt-3">
                        <div class="card-header">
                            <div class="ms-auto">
                                <a href="{{ route('dashboard') }}" class="btn btn-warning pull-left">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                                </a>
                                <a href="{{ route('kaprodi.kelas.add-students', $idKelas) }}"
                                    class="btn btn-primary pull-right">
                                    <i class="fa fa-plus"></i> Tambah Mahasiswa
                                </a>
                            </div>
                        </div>
                    </div><br>

                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Program Studi</th>
                                    <th>Angkatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mahasiswa as $index => $items)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $items->nim }}</td>
                                    <td class="text-left">{{ $items->nama_mhs }}</td>
                                    <td>{{ $items->department->nama }}</td>
                                    <td class="text-center">{{ $items->kelas->angkatan }}</td>
                                    <td class="text-center">
                                        <form
                                            action="{{ route('kaprodi.kelas.remove-student', [$idKelas, $items->id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger text-white btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin mengeluarkan mahasiswa ini dari kelas?')">
                                                <i class="icon ni ni-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-danger text-center">Data tidak tersedia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
