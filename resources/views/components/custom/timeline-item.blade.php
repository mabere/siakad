@props(['step', 'timestamp', 'userName'])

<div class="timeline-item">
    <div class="timeline-status {{ $step === 'approve' ? 'bg-success' : 'bg-info' }}"></div>
    <div class="timeline-date">
        {{ \Carbon\Carbon::parse($timestamp)->format('d F Y H:i') }}
    </div>
    <div class="timeline-content">
        <p class="mb-0 text-primary">{{ ucfirst($step) }} oleh: {{ $userName }}</p>
    </div>
</div>