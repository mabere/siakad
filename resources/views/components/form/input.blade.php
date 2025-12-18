@props([
'name',
'label' => '',
'type' => 'text',
'value' => '',
'required' => false,
])

<div class="form-group">
    @if($label)
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
        class="form-control @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }}>
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>