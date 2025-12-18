@props([
'title' => '',
'value' => 0,
'icon' => 'bar-chart', // default ikon
'bg' => 'primary' // warna ikon, opsional: primary, info, danger, etc
])

<div class="col-4">
    <div class="card card-bordered">
        <div class="card-inner d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-soft fs-13px mb-1">{{ $title }}</h6>
                <h3 class="mb-0 fw-bold">{{ $value }}</h3>
            </div>
            <div class="icon-circle bg-{{ $bg }} text-white">
                <em class="icon ni ni-{{ $icon }}"></em>
            </div>
        </div>
    </div>
</div>
