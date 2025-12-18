<x-main-layout>
    @section('title', 'Daftar Pengumuman')
    <div class="nk-content">
        <x-custom.sweet-alert />
        <div class="card">
            <div class="card-inner">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Daftar Pengumuman</h4>
                    @can('create', App\Models\Announcement::class)
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Tambah</span>
                    </a>
                    @endcan
                </div>
                @if($announcements->isEmpty())
                <div class="alert alert-warning">Belum ada pengumuman.</div>
                @else
                <div class="row g-3">
                    @foreach($announcements as $announcement)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-bordered h-100 shadow-sm">
                            @if($announcement->thumbnail)
                            <img src="{{ asset('storage/' . $announcement->thumbnail) }}" class="card-img-top"
                                alt="Thumbnail">
                            @else
                            <img src="{{ asset('post-image.jpg') }}" class="card-img-top" alt="Default Image">
                            @endif

                            <div class="card-inner d-flex flex-column justify-content-between h-100">
                                <div>
                                    <h5 class="card-title mb-2 text-primary">{{ $announcement->title }}</h5>
                                    <div class="mb-2">
                                        <span class="badge bg-info text-white">{{ $announcement->category }}</span>
                                        <span class="badge bg-secondary text-white text-capitalize">
                                            {{ $announcement->target_role == 'semua' ? 'Semua' :
                                            $announcement->target_role }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        Dibuat oleh: {{ $announcement->createdBy->name }}
                                    </p>
                                    <p class="text-muted small mb-2">
                                        Tanggal: {{ $announcement->created_at->translatedFormat('d M Y') }}
                                    </p>
                                    <p>
                                        Status:
                                        @if($announcement->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="d-flex flex-wrap gap-1 mt-2 mb-2">
                                    @can('view', $announcement)
                                    <a href="{{ route('announcements.show', $announcement->id) }}"
                                        class="btn btn-sm btn-info" title="Lihat">Read more ...

                                    </a>
                                    @endcan

                                    @can('update', $announcement)
                                    <a href="{{ route('announcements.edit', $announcement->id) }}"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <em class="icon ni ni-edit"></em>
                                    </a>
                                    @endcan

                                    @can('delete', $announcement)
                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <em class="icon ni ni-trash"></em>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('toggle', $announcement)
                                    <form action="{{ route('announcements.toggle', $announcement->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-primary"
                                            title="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <em
                                                class="icon ni ni-{{ $announcement->is_active ? 'cross' : 'check' }}-circle"></em>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $announcements->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-main-layout>
