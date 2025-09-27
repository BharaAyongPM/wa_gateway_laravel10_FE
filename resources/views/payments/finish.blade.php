@extends('vendor.layout.master')
@section('title','Status Pembayaran')
@section('content')
<div class="container py-4">

  <div class="card mb-3">
    <div class="card-body">
      <h4 class="mb-0">Status Pembayaran</h4>
    </div>
  </div>

  <div class="alert 
    @if($status==='success') alert-success 
    @elseif($status==='pending') alert-warning 
    @else alert-secondary @endif">
    @if($status==='success')
      Pembayaran berhasil diproses. Terima kasih! 🎉
    @elseif($status==='pending')
      Pembayaran masih menunggu. Selesaikan pembayaran Anda sesuai instruksi di Snap.
    @else
      Status pembayaran tidak diketahui/ditutup. Anda bisa mencoba lagi.
    @endif
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div><strong>Order</strong>: {{ $orderNo ?? '-' }}</div>
      @if($payment)
        <div><strong>Paket</strong>: {{ $payment->plan->name ?? '-' }}</div>
        <div><strong>Total</strong>: Rp {{ number_format($payment->amount,0,',','.') }}</div>
        <div><strong>Status Midtrans</strong>: {{ $payment->status ?? '-' }}</div>
        <div><strong>Tipe Pembayaran</strong>: {{ $payment->payment_type ?? '-' }}</div>
        <div><strong>Paid at</strong>: {{ optional($payment->paid_at)->format('d M Y H:i') ?? '-' }}</div>
      @else
        <div class="text-muted">Detail pembayaran belum tersedia.</div>
      @endif
      <small class="text-muted d-block mt-2">
        Catatan: status final akan mengacu pada notifikasi (webhook) Midtrans. Jika sudah bayar tetapi status belum berubah,
        tunggu beberapa saat lalu refresh halaman device.
      </small>
    </div>
  </div>

  <a href="{{ route('device.index') }}" class="btn btn-primary">Kembali ke Device</a>
</div>
@endsection
