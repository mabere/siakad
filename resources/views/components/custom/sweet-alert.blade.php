@php
$alertTypes = [
'success' => ['title' => 'Success!', 'icon' => 'success', 'class' => 'success'],
'update' => ['title' => 'Update!', 'icon' => 'info', 'class' => 'update'],
'delete' => ['title' => 'Deleted!', 'icon' => 'error', 'class' => 'delete'],
'error' => ['title' => 'Error!', 'icon' => 'error', 'class' => 'error'], // Perbaiki title
];
@endphp

@foreach ($alertTypes as $type => $config)
@if (session($type))
<script>
    document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ $config['title'] }}',
                    text: '{{ session($type) }}',
                    icon: '{{ $config['icon'] }}',
                    confirmButtonText: 'OK',
                    willOpen: () => {
                        Swal.getConfirmButton().classList.add('{{ $config['class'] }}');
                    }
                });
            });
</script>
@endif
@endforeach