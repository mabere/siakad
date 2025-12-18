<x-main-layout>
    @section('title', 'Edit Data PKM')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.pkm.update', $pkm->id) }}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        @php
                        $formFields = [
                        ['label' => 'Judul', 'name' => 'title', 'type' => 'text', 'value' => $pkm->title],
                        ['label' => 'Pendanaan', 'name' => 'pendanaan', 'type' => 'text', 'value' =>
                        $pkm->pendanaan],
                        ['label' => 'Tahun Pelaksanaan PKM', 'name' => 'year', 'type' => 'text', 'value' =>
                        $pkm->year],
                        ];
                        @endphp

                        @foreach ($formFields as $field)
                        <div class="form-group row">
                            <label for="{{ $field['name'] }}" class="col-sm-3 text-end control-label col-form-label">{{
                                $field['label'] }}</label>
                            <div class="col-sm-9">
                                @if ($field['type'] === 'textarea')
                                <textarea class="form-control @error($field['name']) is-invalid @enderror"
                                    name="{{ $field['name'] }}">{{ $field['value'] }}</textarea>
                                @else
                                <input type="{{ $field['type'] }}"
                                    class="form-control @error($field['name']) is-invalid @enderror"
                                    value="{{ $field['value'] }}" name="{{ $field['name'] }}">
                                @endif
                                @error($field['name']) <div class="text-muted">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endforeach

                        <div class="form-group row">
                            <label for="lecturer_id"
                                class="col-sm-3 text-end control-label col-form-label">Penulis</label>
                            <div class="col-sm-9">
                                <select class="form-control form-select @error('lecturer_id') is-invalid @enderror"
                                    name="lecturer_id[]" multiple="multiple">
                                    <optgroup label="Pilih Penulis">
                                        @foreach($lecturers as $dosen)
                                        <option value="{{ $dosen->id }}" @if(in_array($dosen->id,
                                            $pkm->lecturers->pluck('id')->toArray())) selected @endif>
                                            {{ $dosen->nama_dosen }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('lecturer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>