@extends('vendor.layout.master')
@section('title','Rekap & History Pesan')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col">
      <h4 class="mb-0">Rekap & History Kirim Pesan</h4>
      <small class="text-muted">Semua device, termasuk device milik user</small>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif

  {{-- Kotak ringkas --}}
  <div class="row">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Terkirim</div>
      <div class="h5 mb-0">{{ $summary['sent'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Gagal</div>
      <div class="h5 mb-0">{{ $summary['failed'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Queued</div>
      <div class="h5 mb-0">{{ $summary['queued'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Pending</div>
      <div class="h5 mb-0">{{ $summary['pending'] ?? 0 }}</div>
    </div></div></div>
  </div>

  {{-- Filter --}}
  <form class="row g-2 align-items-end mb-3" method="GET">
    <div class="col-md-2">
      <label class="small text-muted">Status</label>
      @php $opt = $status ?? 'all'; @endphp
      <select name="status" class="form-control">
        <option value="all" {{ $opt=='all'?'selected':'' }}>Semua</option>
        @foreach (['sent','failed','queued','pending'] as $s)
          <option value="{{ $s }}" {{ $opt==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="small text-muted">Dari</label>
      <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>
    <div class="col-md-2">
      <label class="small text-muted">Sampai</label>
      <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>
    <div class="col-md-3">
      <label class="small text-muted">Device</label>
      <select name="device_id" class="form-control">
        <option value="">Semua Device</option>
        @foreach ($devices as $d)
          <option value="{{ $d->id }}" {{ (string)$deviceId===(string)$d->id?'selected':'' }}>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  {{-- Tabel pesan --}}
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Device</th>
            <th>Penerima</th>
            <th>Pengirim</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Waktu</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($messages as $i => $m)
            <tr>
              <td>{{ $messages->firstItem() + $i }}</td>
              <td>{{ $m->device->name ?? '-' }}</td>
              <td>
                {{ $m->recipient }}
                <small class="text-muted d-block">{{ strtoupper($m->recipient_type) }}</small>
              </td>
              <td>{{ $m->sender ?? '-' }}</td>
              <td>{{ strtoupper($m->message_type) }}</td>
              <td>
                @php
                  $badge = ['sent'=>'success','failed'=>'danger','queued'=>'secondary','pending'=>'warning'][$m->status] ?? 'light';
                @endphp
                <span class="badge badge-{{ $badge }}">{{ strtoupper($m->status) }}</span>
                @if($m->error_message)
                  <i class="text-muted d-block small">({{ $m->error_message }})</i>
                @endif
              </td>
              <td>
                <div>{{ $m->created_at->format('d M Y H:i') }}</div>
                @if($m->sent_at)
                  <small class="text-muted">Sent: {{ $m->sent_at->format('d M Y H:i') }}</small>
                @endif
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-primary btn-detail" data-id="{{ $m->id }}">Detail</button>
                @if(in_array($m->status,['failed','pending']))
                  <form action="{{ route('admin.messages.retry',$m->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Kirim Ulang</button>
                  </form>
                @endif
                <form action="{{ route('admin.messages.destroy',$m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus log ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">Belum ada data pesan.</td></tr>
          @endforelse
        </tbody>
      </table>
      {{ $messages->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Modal detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Detail Pesan</h5>
      <button class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body"><dl class="row mb-0" id="detailBody"></dl></div>
    <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal">Tutup</button></div>
  </div></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const BASE = '/admin/messages';

  document.body.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const id = btn.dataset.id;

    try {
      const res = await fetch(`${BASE}/${id}`, { headers: {'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' });
      if (!res.ok) throw new Error('Gagal ambil detail');
      const d = await res.json();

      const html = `
        <dt class="col-sm-3">Device</dt><dd class="col-sm-9">${d.device?.name || '-'}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">${d.user?.name || '-'}</dd>
        <dt class="col-sm-3">Pengirim</dt><dd class="col-sm-9">${d.sender || '-'}</dd>
        <dt class="col-sm-3">Penerima</dt><dd class="col-sm-9">${d.recipient} (${(d.recipient_type||'-').toUpperCase()})</dd>
        <dt class="col-sm-3">Jenis</dt><dd class="col-sm-9">${(d.message_type||'-').toUpperCase()}</dd>
        <dt class="col-sm-3">Konten</dt><dd class="col-sm-9">${(d.content||'').replaceAll('<','&lt;')}</dd>
        ${d.media_url ? `<dt class="col-sm-3">Media</dt><dd class="col-sm-9"><a href="${d.media_url}" target="_blank">Lihat Media</a></dd>` : ''}
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">${(d.status||'-').toUpperCase()}</dd>
        <dt class="col-sm-3">Provider</dt><dd class="col-sm-9">${d.provider || '-'}</dd>
        <dt class="col-sm-3">Error</dt><dd class="col-sm-9">${d.error_message || '-'}</dd>
        <dt class="col-sm-3">Retry</dt><dd class="col-sm-9">${d.retry_count ?? 0}</dd>
        <dt class="col-sm-3">Scheduled</dt><dd class="col-sm-9">${d.scheduled_at || '-'}</dd>
        <dt class="col-sm-3">Sent At</dt><dd class="col-sm-9">${d.sent_at || '-'}</dd>
        <dt class="col-sm-3">Created</dt><dd class="col-sm-9">${d.created_at}</dd>
      `;
      document.getElementById('detailBody').innerHTML = html;

      if (window.bootstrap?.Modal) bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail')).show();
      else $('#modalDetail').modal('show');
    } catch (err) {
      console.error(err);
      alert('Tidak bisa membuka detail.');
    }
  });
});
</script>
@endpush
