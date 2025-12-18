<x-main-layout>
    @section('title', 'Daftar Dosen')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Dosen - {{ $department->nama }}</h4>
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Session Messages -->
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

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="zero_config" class="table table-hover table-bordered">
                                <thead class="bg-info text-white">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">NIDN</th>
                                        <th class="text-left">Nama Dosen</th>
                                        <th class="text-left">Email</th>
                                        <th class="text-left">Telepon</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dosen as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $item->nidn }}</td>
                                        <td class="text-left">{{ $item->nama_dosen }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->telp }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('kaprodi.dosen.show', $item->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-danger py-4">
                                            <i class="fas fa-exclamation-circle me-2"></i> Data dosen tidak tersedia
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>