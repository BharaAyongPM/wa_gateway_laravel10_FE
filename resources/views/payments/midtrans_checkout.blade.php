@extends('vendor.layout.master')
@section('title','Checkout')
@section('content')
<div class="container py-4">
  <h4 class="mb-3">Checkout</h4>

  <div class="card mb-3">
    <div class="card-body">
      <div><strong>Order</strong>: {{ $payment->order_no }}</div>
      <div><strong>Device</strong>: {{ $device->name }}</div>
      <div><strong>Paket</strong>: {{ $plan->name }}</div>
      <div><strong>Total</strong>: Rp {{ number_format($payment->amount,0,',','.') }}</div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h6>Syarat & Ketentuan</h6>
      <div class="border rounded p-2 mb-2" style="max-height:150px; overflow:auto;">
        <p class="small mb-2">
          Dengan melanjutkan pembayaran, Anda menyetujui Ketentuan Layanan, Kebijakan Privasi, dan Perjanjian Lisensi layanan WA Gateway.
        </p>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="agree">
        <label class="form-check-label" for="agree">Saya setuju dengan Syarat & Ketentuan</label>
      </div>
    </div>
  </div>

  <div class="mb-3">
    <button id="btnPay" type="button" class="btn btn-primary">Bayar Sekarang</button>
    <a href="{{ route('device.index') }}" class="btn btn-light">Batal</a>
  </div>

  {{-- Fallback container untuk embed mode (jika popup gagal) --}}
  <div id="snap-embed-container" class="d-none">
    <div class="card">
      <div class="card-header">Pembayaran (Embed Mode)</div>
      <div class="card-body">
        <div id="snap-container"></div>
      </div>
    </div>
  </div>
</div>

{{-- Snap.js SANDBOX --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ $clientKey }}"></script>

<script>
(function(){
  const snapToken   = @json($snapToken);
  const clientKey   = @json($clientKey);

  function diagLog(){
    console.group('[MIDTRANS DIAG]');
    console.log('clientKey:', clientKey);
    console.log('snapToken:', snapToken);
    console.log('window.snap:', typeof window.snap, window.snap);
    console.groupEnd();
  }

  function ensureReady(){
    // Minimal checks
    if(!document.getElementById('btnPay')) { console.error('btnPay not found'); return false; }
    if(!snapToken || typeof snapToken !== 'string' || snapToken.length < 10){
      alert('Token pembayaran tidak valid. Silakan kembali dan coba lagi.');
      console.error('Invalid snapToken:', snapToken);
      return false;
    }
    if(typeof window.snap === 'undefined'){
      alert('Script pembayaran belum termuat. Coba refresh halaman (Ctrl+R).');
      console.error('window.snap is undefined. Check network tab: snap.js loaded?');
      return false;
    }
    return true;
  }

  function openPopup(){
    window.snap.pay(snapToken, {
      onSuccess: function(result){
        console.log('Snap success', result);
        window.location = "{{ route('payments.midtrans.finish') }}?status=success&order={{ $payment->order_no }}";
      },
      onPending: function(result){
        console.log('Snap pending', result);
        window.location = "{{ route('payments.midtrans.finish') }}?status=pending&order={{ $payment->order_no }}";
      },
      onError: function(result){
        console.error('Snap error', result);
        alert('Pembayaran gagal. Silakan coba lagi atau gunakan metode lain.');
        // fallback to embed
        openEmbed();
      },
      onClose: function(){
        console.log('Snap closed by user');
      }
    });
  }

  function openEmbed(){
    // Tampilkan container embed
    document.getElementById('snap-embed-container').classList.remove('d-none');
    window.snap.embed(snapToken, {
      embedId: 'snap-container',
      onSuccess: function(result){
        console.log('Embed success', result);
        window.location = "{{ route('payments.midtrans.finish') }}?status=success&order={{ $payment->order_no }}";
      },
      onPending: function(result){
        console.log('Embed pending', result);
        window.location = "{{ route('payments.midtrans.finish') }}?status=pending&order={{ $payment->order_no }}";
      },
      onError: function(result){
        console.error('Embed error', result);
        alert('Pembayaran gagal. Silakan refresh halaman.');
      }
    });
  }

  function handlePayClick(e){
    if(!document.getElementById('agree').checked){
      alert('Anda harus menyetujui Syarat & Ketentuan.');
      return;
    }
    if(!ensureReady()) return;

    try {
      openPopup();
    } catch (err) {
      console.error('openPopup threw error:', err);
      // Coba fallback embed
      try { openEmbed(); } catch (e2) { console.error('openEmbed failed:', e2); }
    }
  }

  // Pastikan binding setelah DOM ready
  document.addEventListener('DOMContentLoaded', function(){
    diagLog();
    const btn = document.getElementById('btnPay');
    if(btn){
      btn.addEventListener('click', handlePayClick);
    } else {
      console.error('btnPay not found on DOMContentLoaded');
    }
  });
})();
</script>
@endsection