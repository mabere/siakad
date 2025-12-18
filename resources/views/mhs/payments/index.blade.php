<x-main-layout>
    @section('title', 'Status Pembayaran')
    <div class="nk-content">
        <div class="card">
            <div class="card-inner">
                <h4>Tagihan UKT</h4>
                @foreach($payments as $payment)
                <div class="card mb-3">
                    <div class="card-body">
                        <p>Semester: {{ $payment->academicYear->ta }} - {{ $payment->academicYear->semester }}</p>
                        <p>Jumlah: Rp {{ number_format($payment->amount, 0) }}</p>
                        <p>Status: {{ $payment->status }}</p>
                        <p>Verifikasi: {{ $payment->verification_status }}</p>
                        @if($payment->status == 'BELUM LUNAS' && $payment->verification_status == 'PENDING')
                        <form action="{{ route('student.payments.upload-proof') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="proof" required>
                            <button type="submit" class="btn btn-primary">Upload Bukti</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-main-layout>