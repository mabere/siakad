<div class="tab-pane fade {{ $active === 'detail' ? 'show active' : '' }}" id="tab-detail" role="tabpanel">
    <div class="nk-block">
        <div class="row g-3">
            @foreach ($fields as $label => $value)
            <div class="data-item p-0 px-3">
                <div class="data-col">
                    <span class="data-label">{{ strtoupper($label) }}</span>
                    {{-- <span class="data-value text-soft">{{ $value }}</span> --}}
                    <span class="data-value text-soft">{!! $value !!}</span>
                </div>
            </div>
            @endforeach
        </div>

        @if (Auth::user()->hasRole(['dosen', 'kaprodi', 'dekan', 'staff']))
        <div class="text-end mt-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editBioModal">
                <em class="icon ni ni-edit"></em> Edit Data Diri
            </button>
        </div>
        @include('backend.profile.partials.edit-form.dosen', ['dosen' => Auth::user()->lecturer])
        @endif

        @if (Auth::user()->hasRole('mahasiswa'))
        <div class="text-end mt-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editMahasiswaModal">
                <em class="icon ni ni-edit"></em> Edit Data Diri
            </button>
        </div>
        @include('backend.profile.partials.edit-form.mahasiswa', ['mahasiswa' => Auth::user()->student])
        @endif

        @if (Auth::user()->hasRole('staff'))
        <div class="text-end mt-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editStaffModal">
                <em class="icon ni ni-edit"></em> Edit Data Diri
            </button>
        </div>
        @include('backend.profile.partials.edit-form.staff', ['staff' => Auth::user()->employee])
        @endif

    </div>
</div>
