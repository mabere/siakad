<x-main-layout>
    @section('title', 'Detail Pengumuman')

    <div class="nk-content">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title mb-3 text-primary">{{ $announcement->title }}</h3>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-secondary">{{ $announcement->category }}</span>
                                <span class="badge bg-info text-dark">Target: {{ ucfirst($announcement->target_role)
                                    }}</span>
                                @if($announcement->faculty)
                                <span class="badge bg-primary">Fakultas: {{ $announcement->faculty->nama }}</span>
                                @endif
                                @if($announcement->department)
                                <span class="badge bg-success">Prodi: {{ $announcement->department->nama }}</span>
                                @endif
                                @if($announcement->kelas)
                                <span class="badge bg-dark">Kelas: {{ $announcement->kelas->name }}</span>
                                @endif
                            </div>

                            <p class="text-muted mb-2">
                                <em class="icon ni ni-calendar"></em>
                                Diterbitkan: {{ $announcement->created_at->translatedFormat('d F Y') }}
                            </p>
                            <p class="text-muted mb-4">
                                <em class="icon ni ni-user-circle"></em>
                                Oleh: <strong>{{ $announcement->createdBy->name }}</strong>
                            </p>

                            <hr>
                            <div class="announcement-content fs-15px text-mute">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>

                        <div class="p-3">
                            @can('update', $announcement)
                            <a href="{{ route('announcements.edit', $announcement->id) }}"
                                class="btn btn-md btn-outline-warning mt-4" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Edit">
                                <em class="icon ni ni-edit"></em>
                            </a>
                            @endcan
                            <a href="{{ route('announcements.index') }}" class="btn btn-md btn-outline-danger mt-4"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Kembali">
                                <em class="icon ni ni-reply"></em>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted mb-3">Info Tambahan</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>ID Pengumuman:</strong> {{ $announcement->id }}</li>
                                <li class="mb-2"><strong>Status Aktif:</strong>
                                    <span class="badge bg-{{ $announcement->is_active ? 'success' : 'danger' }}">
                                        {{ $announcement->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </li>
                                @if($announcement->createdBy)
                                <li class="mb-2"><strong>Oleh:</strong> {{ $announcement->createdBy->name }}</li>
                                <li class="mb-2"><strong>Email:</strong> {{ $announcement->createdBy->email }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .announcement-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .card-title {
            font-weight: 700;
        }

        .badge {
            font-size: 0.8rem;
        }
    </style>
</x-main-layout>
