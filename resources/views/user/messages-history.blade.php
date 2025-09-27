@extends('vendor.layout.master')
@section('title','RIWAYAT PESAN SAYA')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Riwayat Pesan</h4>
    <div class="small text-muted">
      <span class="me-3">Total: {{ $stats['all'] }}</span>
      <span class="me-3 text-success">Sent: {{ $stats['sent'] }}</span>
      <span class="me-3 text-danger">Failed: {{ $stats['failed'] }}</span>
      <span class="me-3 text-secondary">Queued: {{ $stats['queued'] }}</span>
    </div>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('user.messages.history') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Periode: Dari</label>
          <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Sampai</label>
          <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">Semua</option>
            <option value="sent"   {{ $status==='sent' ? 'selected' : '' }}>Terkirim</option>
            <option value="failed" {{ $status==='failed' ? 'selected' : '' }}>Gagal</option>
            <option value="queued" {{ $status==='queued' ? 'selected' : '' }}>Antri</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Device</label>
          <select name="device_id" class="form-select">
            <option value="">Semua</option>
            @foreach($devices as $d)
              <option value="{{ $d->id }}" {{ (string)$deviceId===(string)$d->id ? 'selected' : '' }}>
                {{ $d->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Terapkan</button>
          <a class="btn btn-outline-secondary" href="{{ route('user.messages.history') }}">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
          <thead>
            <tr>
              <th style="width: 160px;">Waktu</th>
              <th>Device</th>
              <th>Recipient</th>
              <th>Type</th>
              <th>Status</th>
              <th>Provider</th>
              <th>Isi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($messages as $m)
              <tr>
                <td>{{ $m->created_at?->format('d M Y H:i') }}</td>
                <td>{{ $m->device?->name ?? '-' }}</td>
                <td>
                  @if($m->recipient_type === 'group')
                    <span class="badge bg-info me-1">Group</span>
                  @elseif(in_array($m->recipient_type, ['number','private','personal']))
                    <span class="badge bg-secondary me-1">Number</span>
                  @endif
                  {{ $m->recipient }}
                </td>
                <td>{{ $m->message_type }}</td>
                <td>
                  @switch($m->status)
                    @case('sent')   <span class="badge bg-success">Sent</span> @break
                    @case('failed') <span class="badge bg-danger">Failed</span> @break
                    @case('queued') <span class="badge bg-warning text-dark">Queued</span> @break
                    @default        <span class="badge bg-light text-dark">{{ $m->status }}</span>
                  @endswitch
                </td>
                <td>{{ $m->provider ?? '-' }}</td>
                <td style="max-width: 360px;">
                  @if($m->media_url)
                    <div class="small text-muted">{{ Str::limit($m->content, 120) }}</div>
                    <a href="{{ $m->media_url }}" target="_blank" class="small">Lihat media</a>
                  @else
                    {{ Str::limit($m->content, 140) }}
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-center">
  {{ $messages->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
<style>
    /* letakkan di CSS custom kamu */
.pagination .page-link {
  padding: .25rem .5rem;
  font-size: 0.875rem;
}
.pagination .page-item.active .page-link {
  background-color: #198754; /* hijau Bootstrap success */
  border-color: #198754;
}

</style>
    </div>
  </div>
</div>
@endsection
