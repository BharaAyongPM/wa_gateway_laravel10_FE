@extends('vendor.layout.master')
@section('title','Broadcast Scheduler')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Broadcast Scheduler</h4>
      <small class="text-muted">Admin dapat memantau semua broadcast. Buat/edit/hapus hanya untuk device milik user role admin.</small>
    </div>
    {{-- Tombol tambah: tetap ada, tapi device & user di dropdown dibatasi ke role admin --}}
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
      <i class="fas fa-plus"></i> Tambah Broadcast (Admin Only)
    </button>
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

  {{-- Summary --}}
  <div class="row mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">Total</div>
      <div class="h5 mb-0">{{ $summary['total'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">Active</div>
      <div class="h5 mb-0">{{ $summary['active'] ?? 0 }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">Inactive</div>
      <div class="h5 mb-0">{{ $summary['inactive'] ?? 0 }}</div>
    </div></div></div>
  </div>

  {{-- Filter --}}
  <form class="row g-2 align-items-end mb-3" method="GET">
    <div class="col-md-2">
      <label class="small text-muted">Status</label>
      @php $opt = $status ?? 'all'; @endphp
      <select name="status" class="form-control">
        <option value="all" {{ $opt=='all'?'selected':'' }}>Semua</option>
        <option value="active" {{ $opt=='active'?'selected':'' }}>Active</option>
        <option value="inactive" {{ $opt=='inactive'?'selected':'' }}>Inactive</option>
      </select>
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
    <div class="col-md-3">
      <label class="small text-muted">User</label>
      <select name="user_id" class="form-control">
        <option value="">Semua User</option>
        @foreach ($users as $u)
          <option value="{{ $u->id }}" {{ (string)$userId===(string)$u->id?'selected':'' }}>
            {{ $u->name }} ({{ $u->role }})
          </option>
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
    <div class="col-md-2 mt-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  {{-- Tabel --}}
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Device</th>
            <th>User</th>
            <th>Pesan</th>
            <th>Grup/Target</th>
            <th>Waktu Kirim</th>
            <th>Status</th>
            <th style="width:220px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($broadcasts as $i => $b)
            @php $isAdminOwner = ($b->user->role ?? '') === 'admin'; @endphp
            <tr>
              <td>{{ $broadcasts->firstItem() + $i }}</td>
              <td>{{ $b->device->name ?? '-' }}</td>
              <td>{{ $b->user->name ?? '-' }} <small class="text-muted">({{ $b->user->role ?? '-' }})</small></td>
              <td class="text-truncate" style="max-width:280px;">{{ Str::limit($b->message, 120) }}</td>
              <td>
                @if(is_array($b->groups))
                  <span class="badge badge-light">{{ count($b->groups) }} target</span>
                  <details><summary class="small text-muted">lihat</summary>
                    <div style="max-width:260px;white-space:normal;">
                      @foreach($b->groups as $g)
                        <div><small>{{ $g }}</small></div>
                      @endforeach
                    </div>
                  </details>
                @else
                  -
                @endif
              </td>
              <td>{{ $b->send_time ? \Carbon\Carbon::parse($b->send_time)->format('H:i') : '-' }}</td>
              <td>
                <span class="badge badge-{{ $b->active?'success':'secondary' }}">{{ $b->active?'ACTIVE':'INACTIVE' }}</span>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-primary btn-detail" data-id="{{ $b->id }}">Detail</button>

                @if($isAdminOwner)
                  <button type="button" class="btn btn-sm btn-outline-info btn-edit" data-id="{{ $b->id }}">Edit</button>
                  <form action="{{ route('admin.broadcasts.destroy',$b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus broadcast ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                @endif

                {{-- toggle: selalu tampil; tetapi aktivasi non-admin diproteksi di server --}}
                <form action="{{ route('admin.broadcasts.toggle',$b->id) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm btn-outline-warning">{{ $b->active?'Nonaktifkan':'Aktifkan' }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">Belum ada broadcast.</td></tr>
          @endforelse
        </tbody>
      </table>

      {{ $broadcasts->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Modal Create (admin-only devices/users) --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.broadcasts.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Broadcast (Admin Only)</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.broadcasts._form', ['mode'=>'create','adminDevices'=>$adminDevices,'users'=>$users])
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal" type="button">Batal</button>
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit (hanya untuk broadcast milik user role admin) --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formEdit" method="POST" action="#">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Broadcast</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.broadcasts._form', ['mode'=>'edit','adminDevices'=>$adminDevices,'users'=>$users])
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal" type="button">Batal</button>
        <button class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Detail Broadcast</h5>
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
  const BASE = '/admin/broadcasts';

  // DETAIL
  document.body.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const id = btn.dataset.id;

    try {
      const res = await fetch(`${BASE}/${id}`, { headers: {'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' });
      if (!res.ok) throw new Error('Gagal ambil detail.');
      const d = await res.json();

      const groupsHtml = Array.isArray(d.groups) && d.groups.length
        ? d.groups.map(g => `<div><small>${g}</small></div>`).join('')
        : '-';

      const html = `
        <dt class="col-sm-3">Device</dt><dd class="col-sm-9">${d.device?.name || '-'}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">${d.user?.name || '-'} (${d.user?.role || '-'})</dd>
        <dt class="col-sm-3">Pesan</dt><dd class="col-sm-9">${(d.message||'').replaceAll('<','&lt;')}</dd>
        <dt class="col-sm-3">Groups</dt><dd class="col-sm-9">${groupsHtml}</dd>
        <dt class="col-sm-3">Send Time</dt><dd class="col-sm-9">${d.send_time || '-'}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">${d.active ? 'ACTIVE' : 'INACTIVE'}</dd>
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

  // EDIT (hanya untuk admin-owned; server juga validasi)
  document.body.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-edit');
    if (!btn) return;
    const id = btn.dataset.id;

    try {
      const res = await fetch(`${BASE}/${id}`, { headers: {'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' });
      if (!res.ok) throw new Error('Gagal ambil data.');
      const d = await res.json();

      // set action
      document.getElementById('formEdit').setAttribute('action', `${BASE}/${id}`);

      // isi field
      document.getElementById('edit_message').value = d.message || '';
      document.getElementById('edit_send_time').value = (d.send_time || '').slice(0,5); // HH:mm

      // groups array → textarea (1 per baris)
      document.getElementById('edit_groups').value = Array.isArray(d.groups) ? d.groups.join('\n') : '';

      // dropdown device/user (hanya daftar admin devices diberikan dari server)
      document.getElementById('edit_device_id').value = d.device_id || '';
      document.getElementById('edit_user_id').value   = d.user_id || '';
      document.getElementById('edit_active').checked  = !!d.active;

      if (window.bootstrap?.Modal) bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit')).show();
      else $('#modalEdit').modal('show');
    } catch (err) {
      console.error(err); alert('Tidak bisa membuka form edit.');
    }
  });
});
</script>
@endpush
