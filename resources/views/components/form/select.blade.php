@props([
'name',
'label' => '',
'options' => [],
'value' => null,
'id' => null,
'required' => false,
])

@php
$inputId = $id ?? $name;
@endphp

<div class="form-group">
    @if($label)
    <label for="{{ $inputId }}" class="form-label">{{ $label }} @if($required)<span
            class="text-danger">*</span>@endif</label>
    @endif
    <select name="{{ $name }}" id="{{ $inputId }}" class="form-control form-select @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}>
        <option value="">-- Pilih --</option>
        @foreach($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" {{ (old($name, $value)==$optionValue) ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
        @endforeach
    </select>
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>