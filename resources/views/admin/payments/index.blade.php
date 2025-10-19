@extends('vendor.layout.master')

@section('title', 'Payments')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col">
      <h4 class="mb-0">Payments</h4>
      <small class="text-muted">Monitor transaksi langganan (manual & Midtrans)</small>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif

  {{-- Summary Cards --}}
  <div class="row">
    <div class="col-md-2"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Paid</div>
      <div class="h5 mb-0">{{ $summary['paid'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-2"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Pending</div>
      <div class="h5 mb-0">{{ $summary['pending'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-2"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Failed</div>
      <div class="h5 mb-0">{{ $summary['failed'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-2"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Expired</div>
      <div class="h5 mb-0">{{ $summary['expired'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
      <div class="text-muted small">Total Paid Amount</div>
      <div class="h5 mb-0">Rp {{ number_format($summary['total_paid_amount'] ?? 0, 0, ',', '.') }}</div>
    </div></div></div>
  </div>

  {{-- Filter --}}
  <form class="row g-2 align-items-end mb-3" method="GET">
    <div class="col-md-2">
      <label class="small text-muted">Status</label>
      @php $opt = $status ?? 'all'; @endphp
      <select name="status" class="form-control">
        <option value="all" {{ $opt=='all'?'selected':'' }}>Semua</option>
        @foreach (['paid','pending','failed','expired','refunded'] as $s)
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
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  {{-- Table --}}
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Transaction</th>
            <th>User</th>
            <th>Plan</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Status</th>
            <th>Paid At</th>
            <th>Tanggal</th>
            <th style="width:140px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $i => $p)
            <tr>
              <td>{{ $payments->firstItem() + $i }}</td>
              <td>{{ $p->transaction_id ?? '-' }}</td>
              <td>{{ $p->user->name ?? '-' }}</td>
              <td>{{ $p->plan->name ?? '-' }}</td>
              <td>Rp {{ number_format($p->amount ?? 0, 0, ',', '.') }}</td>
              <td>{{ strtoupper($p->payment_type ?? '-') }}</td>
              <td>
                @php
                  $map = [
                    'paid' => 'success', 'pending'=>'warning',
                    'failed'=>'danger', 'expired'=>'secondary', 'refunded'=>'info'
                  ];
                @endphp
                <span class="badge badge-{{ $map[$p->status] ?? 'light' }}">{{ strtoupper($p->status ?? '-') }}</span>
              </td>
              <td>{{ $p->paid_at ? $p->paid_at->format('d M Y H:i') : '-' }}</td>
              <td>{{ $p->created_at->format('d M Y H:i') }}</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-primary btn-detail" data-id="{{ $p->id }}">
                  Detail
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-update" data-id="{{ $p->id }}">
                  Update
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center text-muted">Belum ada data pembayaran.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $payments->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Detail Payment</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0" id="detailBody"></dl>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>

{{-- Modal Update Status --}}
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formUpdate" method="POST" action="#">
      @csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Update Status Payment</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="upd_status" class="form-control" required>
            @foreach (['paid','pending','failed','expired','refunded'] as $s)
              <option value="{{ $s }}">{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <small class="text-muted">Perubahan manual ini akan mengupdate kolom <code>status</code> dan otomatis set <code>paid_at</code> jika status menjadi <b>paid</b>.</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal" type="button">Batal</button>
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
  const BASE = '/admin/payments'; // ganti ke '/payments' kalau TIDAK pakai prefix admin

  // DETAIL
  document.body.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const id = btn.dataset.id;

    try {
      const res = await fetch(`${BASE}/${id}`, {
        headers: { 'X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin'
      });
      if (!res.ok) throw new Error('Gagal ambil detail');
      const d = await res.json();

      const html = `
        <dt class="col-sm-3">Transaction</dt><dd class="col-sm-9">${d.transaction_id ?? '-'}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">${(d.user?.name || '-') + (d.user?.email ? ' ('+d.user.email+')' : '')}</dd>
        <dt class="col-sm-3">Plan</dt><dd class="col-sm-9">${d.plan?.name || '-'}</dd>
        <dt class="col-sm-3">Amount</dt><dd class="col-sm-9">Rp ${Number(d.amount||0).toLocaleString('id-ID')}</dd>
        <dt class="col-sm-3">Type</dt><dd class="col-sm-9">${(d.payment_type || '-').toUpperCase()}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">${(d.status || '-').toUpperCase()}</dd>
        <dt class="col-sm-3">Paid At</dt><dd class="col-sm-9">${d.paid_at || '-'}</dd>
        <dt class="col-sm-3">Created</dt><dd class="col-sm-9">${d.created_at}</dd>
        <dt class="col-sm-3">Updated</dt><dd class="col-sm-9">${d.updated_at}</dd>
      `;
      document.getElementById('detailBody').innerHTML = html;

      if (window.bootstrap?.Modal) bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail')).show();
      else $('#modalDetail').modal('show');
    } catch (err) {
      console.error(err); alert('Tidak bisa membuka detail.');
    }
  });

  // UPDATE STATUS
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-update');
    if (!btn) return;
    const id = btn.dataset.id;

    document.getElementById('upd_status').value = 'pending';
    document.getElementById('formUpdate').setAttribute('action', `${BASE}/${id}`);

    if (window.bootstrap?.Modal) bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUpdate')).show();
    else $('#modalUpdate').modal('show');
  });
});
</script>


