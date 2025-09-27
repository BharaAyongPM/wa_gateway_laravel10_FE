@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
<div class="container mt-5">
    <h3>Manajemen Device WhatsApp</h3>

    {{-- Tambah Device (admin bisa set user_id) --}}
    <form action="{{ route('device.create') }}" method="POST" class="mb-3">
        @csrf
        @if(auth()->user()->role === 'admin')
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted">User ID (opsional)</label>
                    <input type="number" name="user_id" class="form-control" placeholder="Masukkan User ID">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success mt-4">Tambah Device</button>
                </div>
            </div>
        @else
            <button class="btn btn-success">Tambah Device</button>
        @endif
    </form>

    {{-- Alert flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Status</th>
                <th>Last Connected</th>
                <th>Plan / Trial</th>
                <th>Sisa Kuota</th>
                <th>Expired</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($devices as $device)
                @php
                    // asumsikan relasi: $device->subscription()->whereDate('expired_at','>=',now())->first()
                    $sub = $device->subscription ?? null;
                    $badgeMap = ['connected'=>'success','online'=>'success','pending'=>'warning','offline'=>'secondary','error'=>'danger'];
                    $badge = $badgeMap[$device->status] ?? 'light';
                @endphp
                <tr>
                    <td>{{ $device->name }}</td>
                    <td>
                        <span class="badge bg-{{ $badge }}">{{ strtoupper($device->status ?? '-') }}</span>
                    </td>
                    <td>{{ $device->last_connected_at ? \Carbon\Carbon::parse($device->last_connected_at)->format('d M Y H:i') : '-' }}</td>
                   <td>
    @php $sub = $device->activeSubscription; @endphp
    {{ $sub ? $sub->displayName() : '-' }}
</td>
                   <td>
    {{ $sub ? $sub->remaining_quota : '-' }}
</td>
                    <td>
    @if($sub && $sub->expired_at)
        {{ $sub->expired_at->format('d M Y') }}
    @else
        -
    @endif
</td>
                    <td class="text-nowrap">
                        <a href="{{ route('device.qr', $device->id) }}" class="btn btn-primary btn-sm">QR</a>
                        <button class="btn btn-sm btn-warning" onclick="showApiKeyModal({{ $device->id }})">API Key</button>
                        <a href="{{ route('device.status', $device->id) }}" class="btn btn-info btn-sm">Cek Status</a>
                        <form action="{{ route('device.destroy', $device->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus device ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                        <a href="{{ route('device.upgrade.show', $device->id) }}" class="btn btn-success btn-sm">Upgrade</a>
                    </td>
                </tr>
            @endforeach
            @if($devices->isEmpty())
                <tr><td colspan="7" class="text-center text-muted">Belum ada device.</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Modal API Key --}}
<div class="modal fade" id="apiKeyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">API Key Device</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="input-group">
          <input type="text" class="form-control" id="apiKeyValue" readonly>
          <button class="btn btn-outline-secondary" onclick="copyApiKey()">Salin</button>
        </div>
        <small class="text-muted mt-2 d-block">Gunakan API Key ini untuk integrasi aplikasi eksternal.</small>
      </div>
    </div>
  </div>
</div>

{{-- Script --}}
<script>
function showApiKeyModal(deviceId) {
  fetch(`/device/${deviceId}/generate-apikey`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    credentials: 'same-origin'
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById("apiKeyValue").value = data.api_key || '';
    // Bootstrap 5 / 4 fallback
    var el = document.getElementById('apiKeyModal');
    if (window.bootstrap && window.bootstrap.Modal) {
      new bootstrap.Modal(el).show();
    } else if (window.$ && $.fn.modal) {
      $('#apiKeyModal').modal('show');
    }
  })
  .catch(() => alert("Gagal mengambil API Key"));
}

function copyApiKey() {
  const input = document.getElementById("apiKeyValue");
  input.select();
  input.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(input.value || '');
  alert("API Key berhasil disalin!");
}
</script>
@endsection
