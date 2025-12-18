@props([
'label',
'name',
'id' => $name,
'required' => false,
])

<div class="form-group">
    <label for="{{ $id }}" class="form-label">{{ $label }}{{ $required ? ' *' : '' }}</label>
    <textarea id="{{ $id }}" name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'form-control']) }}
    ></textarea>
</div>