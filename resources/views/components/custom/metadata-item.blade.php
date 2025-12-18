@props(['icon', 'label'])

<div class="d-flex align-items-center mb-3">
    <div class="icon-md bg-light rounded-circle me-3">
        <em class="icon ni {{ $icon }} text-indigo"></em>
    </div>
    <div>
        <h6 class="mb-1">{{ $label }}</h6>
        <p class="mb-0 text-dark">{{ $slot }}</p>
    </div>
</div>