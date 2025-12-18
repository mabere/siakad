<x-main-layout>
    @section('title', 'Daftar Kelas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Kelas - {{ $department->nama }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary pull-left">
                            <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
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

                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Dosen Wali</th>
                                    <th>Angkatan</th>
                                    <th>Jumlah Mahasiswa</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kelas as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $kelas->firstItem() + $index }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->lecturer->nama_dosen ?? 'Belum Ditugaskan' }}</td>
                                    <td class="text-center">{{ $item->angkatan }}</td>
                                    <td class="text-center">{{ $item->total }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('kaprodi.kelas.show', $item->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-danger text-center">Data kelas tidak tersedia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Navigasi Pagination -->
                    <div class="mt-3">
                        {{ $kelas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>