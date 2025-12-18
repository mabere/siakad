@php
$sidebar = include config_path('sidebar.php');
$menus = $sidebar[auth()->user()->activeRole()] ?? [];
@endphp

@foreach ($menus as $item)
{{-- Heading --}}
@if (isset($item['heading']))
<li class="nk-menu-heading">
    <h6 class="overline-title text-primary-alt">{{ $item['heading'] }}</h6>
</li>

{{-- Komponen kustom --}}
@elseif (isset($item['component']))
@if(view()->exists($item['component']))
<x-dynamic-component :component="$item['component']" />
@endif

{{-- Submenu level 1 --}}
@elseif (isset($item['sub']))
<li class="nk-menu-item has-sub">
    <a href="#" class="nk-menu-link nk-menu-toggle">
        <span class="nk-menu-icon"><em class="icon {{ $item['icon'] ?? 'ni ni-folder' }}"></em></span>
        <span class="nk-menu-text">{{ $item['title'] ?? '-' }}</span>
    </a>
    <ul class="nk-menu-sub">
        @foreach ($item['sub'] as $sub)
        {{-- Submenu level 2 --}}
        @if (isset($sub['sub']))
        <li class="nk-menu-item has-sub">
            <a href="#" class="nk-menu-link nk-menu-toggle">
                <span class="nk-menu-icon"><em class="icon {{ $sub['icon'] ?? 'ni ni-chevron-right' }}"></em></span>
                <span class="nk-menu-text">{{ $sub['title'] ?? '-' }}</span>
            </a>
            <ul class="nk-menu-sub">
                @foreach ($sub['sub'] as $deep)
                @php
                $deepRoute = $deep['route'] ?? null;
                $deepHref = $deepRoute && Route::has($deepRoute) ? route($deepRoute) : '#';
                @endphp
                <li class="nk-menu-item">
                    <a href="{{ $deepHref }}" class="nk-menu-link">
                        <span class="nk-menu-text">{{ $deep['title'] ?? '-' }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        @else
        @php
        $subRoute = $sub['route'] ?? null;
        $subHref = $subRoute && Route::has($subRoute) ? route($subRoute) : '#';
        @endphp
        <li class="nk-menu-item">
            <a href="{{ $subHref }}" class="nk-menu-link">
                <span class="nk-menu-text">{{ $sub['title'] ?? '-' }}</span>
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</li>

{{-- Single item --}}
@else
@php
$itemRoute = $item['route'] ?? null;
$itemHref = $itemRoute && Route::has($itemRoute) ? route($itemRoute) : '#';
@endphp
<li class="nk-menu-item">
    <a href="{{ $itemHref }}" class="nk-menu-link">
        <span class="nk-menu-icon"><em class="icon {{ $item['icon'] ?? 'ni ni-dot' }}"></em></span>
        <span class="nk-menu-text">{{ $item['title'] ?? '-' }}</span>
    </a>
</li>
@endif
@endforeach