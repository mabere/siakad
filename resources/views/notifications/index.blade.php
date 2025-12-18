<x-main-layout>
    @section('title', 'Notifikasi')
    <div class="container">
        <style>
            .nk-notification-item {
                padding: 10px;
            }

            .nk-notification-item:hover {
                background-color: #f5f6fa;
            }

            .nk-notification.bg-light {
                opacity: 0.7;
            }
        </style>
        <h1>Semua Notifikasi</h1>
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
        @endif
        <div class="card">
            <div class="card-body">
                @forelse ($notifications as $notification)
                <div class="nk-notification {{ $notification->read_at ? 'bg-light' : '' }}">
                    <div class="nk-notification-item dropdown-inner">
                        <div class="nk-notification-icon">
                            <em
                                class="icon icon-circle {{ $notification->type == 'App\Notifications\NewAnnouncement' ? 'bg-info-dim ni ni-bell' : (array_key_exists('rejection_reason', $notification->data) && $notification->data['rejection_reason'] ? 'bg-danger-dim ni ni-cross' : 'bg-success-dim ni ni-check') }}"></em>
                        </div>
                        <div class="nk-notification-content">
                            <div class="nk-notification-text">
                                {{ $notification->data['title'] }}
                                @if (array_key_exists('link', $notification->data) && $notification->data['link'])
                                <a href="{{ $notification->data['link'] }}">{{ $notification->data['message'] ?? 'Lihat
                                    detail' }}</a>
                                @else
                                {{ $notification->data['message'] ?? 'Lihat detail' }}
                                @endif
                                @if (array_key_exists('notes', $notification->data) && $notification->data['notes'])
                                <div><small>Catatan: {{ $notification->data['notes'] }}</small></div>
                                @elseif (array_key_exists('rejection_reason', $notification->data) &&
                                $notification->data['rejection_reason'])
                                <div><small>Alasan: {{ $notification->data['rejection_reason'] }}</small></div>
                                @endif
                            </div>
                            <div class="nk-notification-time">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('notifications.delete', $notification->id) }}" class="btn btn-sm btn-danger"
                    onclick="return confirm('Hapus notifikasi?')">Hapus</a>
                @if (!$loop->last)
                <hr class="my-2">
                @endif
                @empty
                <p>Tidak ada notifikasi.</p>
                @endforelse
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-main-layout>