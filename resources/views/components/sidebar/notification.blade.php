<li class="dropdown notification-dropdown me-n1">
    <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
        <div class="icon-status {{ auth()->user()->unreadNotifications->count() > 0 ? 'icon-status-info' : '' }}">
            <em class="icon ni ni-bell"></em>
        </div>
        @if (auth()->check())
        @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp
        @if ($unreadCount > 0)
        <span class="badge rounded-pill bg-danger">
            {{ $unreadCount }}
        </span>
        @endif
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end dropdown-menu-s1">
        <div class="dropdown-head">
            <span class="sub-title nk-dropdown-title">Notifications</span>
            <a href="{{ route('notifications.mark-all-read') }}">Mark All as Read</a>
        </div>
        <div class="dropdown-body">
            @forelse (auth()->user()->unreadNotifications as $notification)
            <div class="nk-notification">
                <div class="nk-notification-item dropdown-inner">
                    <div class="nk-notification-icon">
                        <em
                            class="icon icon-circle {{ array_key_exists('rejection_reason', $notification->data) && $notification->data['rejection_reason'] ? 'bg-danger-dim ni ni-cross' : 'bg-success-dim ni ni-check' }}"></em>
                    </div>
                    <div class="nk-notification-content">
                        <div class="nk-notification-text">
                            {{ $notification->data['title'] }}
                            @if (array_key_exists('link', $notification->data) && $notification->data['link'])
                            <a href="{{ $notification->data['link'] }}">{{ $notification->data['message'] }}</a>
                            @else
                            {{ $notification->data['message'] ?? 'Lihat detail pengumuman' }}
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
            @empty
            <div class="nk-notification">
                <div class="nk-notification-item dropdown-inner text-center">
                    <div class="nk-notification-content">
                        <div class="nk-notification-text">Tidak ada notifikasi baru</div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="dropdown-foot center">
            <a href="{{ route('notifications.all') }}">View All</a>
        </div>
    </div>
</li>