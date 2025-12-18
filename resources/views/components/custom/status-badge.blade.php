@props(['status'])

@php
$config = [
'submitted' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
'processing' => ['label' => 'Diproses', 'class' => 'bg-info'],
'approved' => ['label' => 'Disetujui', 'class' => 'bg-success'],
'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
];
$statusConfig = $config[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-secondary'];
@endphp

<span class="badge {{ $statusConfig['class'] }}">
    {{ $statusConfig['label'] }}
</span>