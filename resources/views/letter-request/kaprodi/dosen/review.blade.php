<x-main-layout>
    @section('title', 'Review Surat #'.$letterRequest->id)
    <div class="container">
        <h1>Review Surat #{{ $letterRequest->id }}</h1>
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <label>Jenis Surat</label>
                        <input type="text" class="form-control" value="{{ $letterRequest->tipeSurat->name }}" readonly>
                    </div>
                    <div class="col-12">
                        <label>Pemohon</label>
                        <input type="text" class="form-control" value="{{ $letterRequest->user->name }}" readonly>
                    </div>
                    @foreach($letterRequest->form_data as $key => $value)
                    <div class="col-12">
                        <label>{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                        <input type="text" class="form-control" value="{{ $value }}" readonly>
                    </div>
                    @endforeach
                    <div class="col-12">
                        <label>Status</label>
                        <span
                            class="badge {{ ['submitted' => 'bg-warning', 'processing' => 'bg-info', 'approved' => 'bg-success', 'rejected' => 'bg-danger'][$letterRequest->status] ?? 'bg-secondary' }}">{{
                            $letterRequest->status }}</span>
                    </div>
                </div>
                <form action="{{ route('kaprodi.request.review.dosen.approve', $letterRequest) }}" method="POST"
                    class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary">Setujui dan Proses</button>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>