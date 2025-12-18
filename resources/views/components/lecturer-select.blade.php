@props([
'number',
'dosen',
'selected_id',
'start',
'end',
'required' => false
])
<div class="col-12 col-md-4">
    <div class="form-group">
        <label class="form-label" for="lecturer{{ $number }}_id">Dosen {{ $number }}</label>
        <div class="form-control-wrap">
            <select class="form-control form-select @error('lecturer' . $number . '_id') is-invalid @enderror"
                name="lecturer{{ $number }}_id" id="lecturer{{ $number }}_id"
                aria-describedby="lecturer{{ $number }}-error" {{ $required ? 'required' : '' }}>
                <option value="">-- Pilih Dosen --</option>
                @foreach ($dosen as $item)
                <option value="{{ $item->id }}" {{ old('lecturer' . $number . '_id' , $selected_id)==$item->id ?
                    'selected' : '' }}>
                    {{ $item->nama_dosen }}
                </option>
                @endforeach
            </select>
            @error('lecturer' . $number . '_id')
            <div class="invalid-feedback" id="lecturer{{ $number }}-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 col-md-4">
    <div class="form-group">
        <label class="form-label" for="lecturer{{ $number }}_start">Minggu Awal</label>
        <div class="form-control-wrap">
            <input type="number" min="1" max="16"
                class="form-control @error('lecturer' . $number . '_start') is-invalid @enderror"
                name="lecturer{{ $number }}_start" id="lecturer{{ $number }}_start"
                value="{{ old('lecturer' . $number . '_start', $start ?? '') }}"
                aria-describedby="lecturer{{ $number }}-start-error" {{ $required ? 'required' : '' }}>
            @error('lecturer' . $number . '_start')
            <div class="invalid-feedback" id="lecturer{{ $number }}-start-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 col-md-4">
    <div class="form-group">
        <label class="form-label" for="lecturer{{ $number }}_end">Minggu Akhir</label>
        <div class="form-control-wrap">
            <input type="number" min="1" max="16"
                class="form-control @error('lecturer' . $number . '_end') is-invalid @enderror"
                name="lecturer{{ $number }}_end" id="lecturer{{ $number }}_end"
                value="{{ old('lecturer' . $number . '_end', $end ?? '') }}"
                aria-describedby="lecturer{{ $number }}-end-error" {{ $required ? 'required' : '' }}>
            @error('lecturer' . $number . '_end')
            <div class="invalid-feedback" id="lecturer{{ $number }}-end-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>