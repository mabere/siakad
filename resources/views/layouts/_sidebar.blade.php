@php
$role = Auth::user()->activeRole();
@endphp

<div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                <em class="icon ni ni-arrow-left"></em>
            </a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu">
                <em class="icon ni ni-menu"></em>
            </a>
        </div>
        <div class="nk-sidebar-brand">
            <a class="user-avater logo-link nk-sidebar-logo" href="{{url('/')}}" class="logo-link">
                <img src="{{ asset('images/light-logo.png') }}" alt="SIAKAD" class="img-fluid logo-light logo-img"
                    style="max-height: 40px" />
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">CONTROL PANEL</h6>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('dashboard') }}"
                            class="nk-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><em
                                class="icon ni ni-grid-fill"></em>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('profile') }}"
                            class="nk-menu-link {{ request()->routeIs('profile') ? 'active' : '' }}"><em
                                class="icon ni ni-account-setting-fill"></em>
                            <span class="nk-menu-text">Profile</span>
                        </a>
                    </li>
                    <x-sidebar.main></x-sidebar.main>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">INFORMASI</h6>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('announcements.index') }}"
                            class="nk-menu-link {{ request()->routeIs('announcements.index') ? 'active' : '' }}"><em
                                class="icon ni ni-bell"></em>
                            <span class="nk-menu-text">Pengumuman</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('calendar.index') }}"
                            class="nk-menu-link {{ request()->routeIs('calendar.index') ? 'active' : '' }}"><em
                                class="icon ni ni-calendar"></em>
                            <span class="nk-menu-text">Kalender Akademik</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nk-menu-text btn btn-default text-danger fw-bold">
                                <span class="nk-menu-icon"><em class="icon ni ni-signout"></em></span>
                                <span class="nk-menu-text">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
