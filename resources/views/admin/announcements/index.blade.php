<x-main-layout>
    <div class="row g-3">
        @foreach($announcements as $announcement)
        <div class="col-md-6 col-lg-4">
            <div class="card card-bordered h-100 shadow-sm">
                @if($announcement->thumbnail)
                <img src="{{ asset('storage/'.$announcement->thumbnail) }}" class="card-img-top" alt="Thumbnail">
                @else
                <img src="{{ asset('images/default-announcement.jpg') }}" class="card-img-top" alt="Default">
                @endif
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title text-primary">{{ $announcement->title }}</h5>
                        <span class="badge bg-info">{{ $announcement->category }}</span>
                        <span class="badge bg-secondary text-capitalize">{{
                            $announcement->target_role=='semua'?'Semua':$announcement->target_role }}</span>
                        <p class="mt-2 small text-muted">{{ $announcement->createdBy->name }} • {{
                            $announcement->created_at->translatedFormat('d M Y') }}</p>
                        <p>Status: {!! $announcement->is_active?
                            '<span class="badge bg-success">Aktif</span>':'<span
                                class="badge bg-danger">Nonaktif</span>' !!}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        @can('view', $announcement)
                        <a href="{{ route('announcements.show',$announcement) }}" class="btn btn-sm btn-info"><em
                                class="icon ni ni-eye"></em></a>
                        @endcan
                        @can('update', $announcement)
                        <a href="{{ route('announcements.edit',$announcement) }}" class="btn btn-sm btn-warning"><em
                                class="icon ni ni-edit"></em></a>
                        @endcan
                        @can('delete', $announcement)
                        <form action="{{ route('announcements.destroy',$announcement) }}" method="POST"
                            onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><em class="icon ni ni-trash"></em></button>
                        </form>
                        @endcan
                        @can('toggle', $announcement)
                        <form action="{{ route('announcements.toggle',$announcement) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-primary"><em
                                    class="icon ni ni-{{ $announcement->is_active?'cross':'check' }}-circle"></em></button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $announcements->links() }}</div>
</x-main-layout>
