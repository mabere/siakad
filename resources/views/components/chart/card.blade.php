@props(['title' => '', 'color' => 'primary'])

<div class="card card-bordered border-0 shadow-sm h-100">
    <div class="card-header text-white"
        style="background: linear-gradient(135deg, var(--bs-{{ $color }}, #{{ $color }}), #{{ $color === 'primary' ? '3366ff' : '666' }});">
        <h5 class="card-title text-white m-0">{{ $title }}</h5>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>