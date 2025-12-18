@props([
'title' => '',
'data' => [],
'id' => 'barChart-' . uniqid()
])

<div class="card card-bordered">
    <div class="card-inner">
        <h6 class="card-title">{{ $title }}</h6>
        <canvas id="{{ $id }}" height="200"></canvas>
    </div>
</div>

@push('scripts')
<script>
    const barCtx{{ $id }} = document.getElementById('{{ $id }}').getContext('2d');
    new Chart(barCtx{{ $id }}, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data->keys()) !!},
            datasets: [{
                label: 'Jumlah',
                data: {!! json_encode($data->values()) !!},
                backgroundColor: '#798bff'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush