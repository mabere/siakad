@props(['label', 'value', 'icon' => null])

<div class="col-12">
    <div class="form-group">
        <label class="form-label">
            @if($icon)
            <em class="icon ni {{ $icon }} me-1"></em>
            @endif
            {{ $label }}
        </label>
        <div class="form-control-wrap">
            <input type="text" class="form-control" value="{{ $value }}" readonly>
        </div>
    </div>
</div>