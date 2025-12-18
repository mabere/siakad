@php
$user = Auth::user();
$activeRole = $user->activeRole();
$roles = $user->getRoleNames();
// Map folder berdasarkan role
$roleFolders = [
'admin' => 'admin',
'kaprodi' => 'dosen',
'dosen' => 'dosen',
'dekan' => 'dosen', // dekan menggunakan folder dosen
'staff' => 'staff',
'ktu' => 'misc',
'mahasiswa' => 'mhs',
];
$folder = $roleFolders[$activeRole] ?? 'default';
$photoFile = $user->photo ?: "$folder.jpg";
$src = asset("storage/images/$folder/$photoFile");
@endphp




<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                    <em class="icon ni ni-menu"></em>
                </a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{ url('/') }}" class="logo-link">
                    <h4>Siakad</h4>
                </a>
            </div>
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    @if(in_array(Auth::user()->activeRole(),['dekan', 'dosen',
                    'mahasiswa','ktu','admin','staff','kaprodi']))
                    <x-sidebar.notification />
                    @endif

                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar">
                                    <div class="user-card">
                                        <div class="user-avatar">
                                            <img src="{{ $src }}" alt="{{ $user->name }}"
                                                class="rounded-circle border-1" width="50" height="45">
                                        </div>
                                    </div>
                                </div>
                                <div class="user-info d-none d-md-block">
                                    <div class="user-status">{{ strtoupper($activeRole) }}</div>
                                    <div class="user-name">{{ $user->name }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="lead-text">{{ $user->name }}</span>
                                        <span class="sub-text">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <h6 class="overline-title">Ganti Peran</h6>
                                @foreach($roles as $role)
                                <form method="POST" action="{{ route('user.setRole') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $role }}">
                                    <button type="submit"
                                        class="dropdown-item {{ $activeRole == $role ? 'text-primary fw-bold' : '' }}">
                                        <em class="icon ni ni-user-switch"></em> {{ ucfirst($role) }}
                                    </button>
                                </form>
                                @endforeach
                            </div>
                            <div class="dropdown-inner">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">
                                        <em class="icon ni ni-signout"></em> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
