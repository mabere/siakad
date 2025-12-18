@props([
'title' => '',
'data' => [],
'id' => 'pieChart-' . uniqid()
])

<div class="card card-bordered">
    <div class="card-inner">
        <h6 class="card-title">{{ $title }}</h6>
        <canvas id="{{ $id }}" height="200"></canvas>
    </div>
</div>

@push('scripts')
<script>
    const pieCtx{{ $id }} = document.getElementById('{{ $id }}').getContext('2d');
    new Chart(pieCtx{{ $id }}, {
        type: 'pie',
        data: {
            labels: {!! json_encode($data->keys()) !!},
            datasets: [{
                data: {!! json_encode($data->values()) !!},
                backgroundColor: ['#798bff', '#ffa353', '#1ee0ac', '#f4bd0e'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush