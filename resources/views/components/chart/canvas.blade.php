@props(['title' => '', 'id'])

<div class="card card-full" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 1rem;">
    <div class="card-inner">
        <div class="card-title-group">
            <div class="card-title">
                <h6 class="title mb-3" style="font-size: 18px; font-weight: bold; color: #1E3A8A;">
                    {{ $title }}
                </h6>
            </div>
        </div>
        <div class="nk-ck" style="padding: 0;">
            <canvas id="{{ $id }}"></canvas>
        </div>
    </div>
</div>