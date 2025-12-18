@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const approveButtons = document.querySelectorAll('.btn-success');
        const rejectButtons = document.querySelectorAll('.btn-danger');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                console.log('Approve button clicked for ID:', this.getAttribute('data-bs-target'));
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', function () {
                console.log('Reject button clicked for ID:', this.getAttribute('data-bs-target'));
            });
        });
    });
</script>
@endpush