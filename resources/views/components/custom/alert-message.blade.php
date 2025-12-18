@if (session('success'))
<div class="alert alert-icon alert-success" role="alert">
    <em class="icon ni ni-check-circle"></em>
    <strong>{{ session('success') }}</strong>
</div>
@endif

@if (session('update'))
<div class="alert alert-icon alert-warning" role="alert">
    <em class="icon ni ni-check-circle"></em>
    <strong>{{ session('update') }}</strong>
</div>
@endif

@if (session('delete'))
<div class="alert alert-icon alert-danger" role="alert">
    <em class="icon ni ni-check-circle"></em>
    <strong>{{ session('delete') }}</strong>
</div>
@endif

@if (session('error'))
<div class="alert alert-icon alert-danger" role="alert">
    <em class="icon ni ni-check-circle"></em>
    <strong>{{ session('error') }}</strong>
</div>
@endif