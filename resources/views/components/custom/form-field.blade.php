@props(['label', 'value', 'danger' => false])

<div class="col-12">
    <label class="form-label">{{ $label }}</label>
    <input type="text" class="form-control {{ $danger ? 'text-danger' : '' }}" value="{{ $value }}" readonly>
</div>