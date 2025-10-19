@extends('vendor.layout.master')
@section('title','Pengaturan Bot')

@section('content')
<div class="container py-4">
  <h4 class="mb-3">Pengaturan Bot Otomatis</h4>

  <form class="row g-3 mb-3" method="GET" action="{{ route('bot.settings') }}">
    <div class="col-md-6">
      <label class="form-label">Pilih Device</label>
      <select name="device_id" class="form-select" onchange="this.form.submit()">
        @foreach($devices as $d)
          <option value="{{ $d->id }}" {{ (string)$deviceId===(string)$d->id ? 'selected' : '' }}>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>
  </form>

  @if($device)
  <form method="POST" action="{{ route('bot.settings.update') }}">
  @csrf
  <input type="hidden" name="device_id" value="{{ $device->id }}">
  <input type="hidden" name="features_present" value="1">  {{-- 🔑 penanda bahwa daftar fitur dikirim --}}

  <div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" value="1" {{ $bot?->is_enabled ? 'checked':'' }}>
    <label class="form-check-label" for="is_enabled">Aktifkan Bot pada device <strong>{{ $device->name }}</strong></label>
  </div>

  <div class="card">
    <div class="card-header">Pilih Fitur</div>
    <div class="card-body">
      <div class="row">
        @foreach($features as $f)
          <div class="col-md-4 mb-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox"
                     name="features[]" value="{{ $f->key }}" id="f_{{ $f->key }}"
                     {{ ($active[$f->key] ?? false) ? 'checked':'' }}>
              <label class="form-check-label" for="f_{{ $f->key }}">
                {{ $f->name }}
                @if($f->description)<small class="text-muted d-block">{{ $f->description }}</small>@endif
              </label>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="mt-3">
    <button class="btn btn-success">Simpan</button>
  </div>
</form>
  @else
    <div class="alert alert-warning">Anda belum memiliki device.</div>
  @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        confirmButtonColor: '#3085d6'
      });
    @endif

    @if(session('error'))
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: @json(session('error')),
        confirmButtonColor: '#d33'
      });
    @endif
  });
</script>
@endsection





