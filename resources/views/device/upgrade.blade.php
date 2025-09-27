@extends('vendor.layout.master')
@section('title', 'Upgrade Device')
@section('content')
<div class="container py-4">
  <h4 class="mb-3">Upgrade Device</h4>
{{-- Flash & validation errors --}}
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  {{-- Info Device --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><strong>Nama Device</strong><br>{{ $device->name }}</div>
        <div class="col-md-4"><strong>Nomor/ID</strong><br>{{ $device->number ?? $device->jid ?? '-' }}</div>
        <div class="col-md-4"><strong>Paket Sekarang</strong><br>{{ $sub ? $sub->plan->name : '-' }}</div>
        <div class="col-md-4"><strong>Kuota</strong><br>{{ $sub ? $sub->remaining_quota : '-' }}</div>
        <div class="col-md-4"><strong>Masa Berlaku</strong><br>{{ $sub && $sub->expired_at ? $sub->expired_at->format('d M Y') : '-' }}</div>
      </div>
    </div>
  </div>

  {{-- Pilih Plan --}}
 <form method="POST" action="{{ route('device.upgrade.checkout', $device->id) }}">
    @csrf
    <div class="row">
      @foreach($plans as $plan)
      <div class="col-md-4">
        <label class="card h-100 p-3 border @error('plan_id') border-danger @enderror" style="cursor:pointer;">
          <div class="d-flex justify-content-between">
            <h5 class="mb-1">{{ $plan->name }}</h5>
            <input type="radio" class="form-check-input mt-1"
                   name="plan_id" value="{{ $plan->id }}" required>
          </div>
          <div class="small text-muted">Durasi: {{ $plan->duration }} hari</div>
          <div class="small text-muted">Kuota: {{ number_format($plan->quota_limit) }}</div>
          <hr>
          <div class="fw-bold">Rp {{ number_format($plan->price,0,',','.') }}</div>
        </label>
      </div>
      @endforeach
    </div>

    <div class="mt-3">
      <a href="{{ route('device.index') }}" class="btn btn-light">Kembali</a>
      <button type="submit" class="btn btn-primary">Lanjut ke Checkout</button>
    </div>
  </form>
</div>
@endsection
