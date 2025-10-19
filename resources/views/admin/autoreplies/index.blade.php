@extends('vendor.layout.master')
@section('title','Auto Reply Rules')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Auto Reply Rules</h4>
      <small class="text-muted">Kelola keyword & respon per device</small>
    </div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
      <i class="fas fa-plus"></i> Tambah Rule
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif

  {{-- Ringkas --}}
  <div class="row mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">Total Rules</div>
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
    <div class="col-md-3">
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
    <div class="col-md-2">
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
            <th>Keyword</th>
            <th>Response (preview)</th>
            <th>Type</th>
            <th>Device</th>
            <th>User</th>
            <th>Status</th>
            <th style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rules as $i => $r)
            <tr>
              <td>{{ $rules->firstItem() + $i }}</td>
              <td><code>{{ $r->keyword }}</code></td>
              <td class="text-truncate" style="max-width:280px;">{{ Str::limit($r->response, 120) }}</td>
              <td>{{ strtoupper($r->type) }}</td>
              <td>{{ $r->device->name ?? '-' }}</td>
              <td>{{ $r->user->name ?? '-' }}</td>
              <td>
                <span class="badge badge-{{ $r->active?'success':'secondary' }}">{{ $r->active?'ACTIVE':'INACTIVE' }}</span>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $r->id }}">Edit</button>
                <form action="{{ route('admin.autoreplies.toggle',$r->id) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm btn-outline-warning">{{ $r->active?'Nonaktifkan':'Aktifkan' }}</button>
                </form>
                <form action="{{ route('admin.autoreplies.destroy',$r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rule ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">Belum ada rule.</td></tr>
          @endforelse
        </tbody>
      </table>
      {{ $rules->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.autoreplies.store') }}">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Tambah Auto Reply</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.autoreplies._form', ['mode'=>'create'])
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal" type="button">Batal</button>
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit (render langsung, isi via JS) --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formEdit" method="POST" action="#">
      @csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Edit Auto Reply</h5>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.autoreplies._form', ['mode'=>'edit'])
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal" type="button">Batal</button>
        <button class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const BASE = '/admin/autoreplies';

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
      document.getElementById('edit_keyword').value = d.keyword || '';
      document.getElementById('edit_response').value = d.response || '';
      document.getElementById('edit_type').value = d.type || 'text';
      document.getElementById('edit_device_id').value = d.device_id || '';
      document.getElementById('edit_user_id').value = d.user_id || '';
      document.getElementById('edit_active').checked = !!d.active;

      if (window.bootstrap?.Modal) bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit')).show();
      else $('#modalEdit').modal('show');
    } catch (err) {
      console.error(err);
      alert('Tidak bisa membuka form edit.');
    }
  });

  // Toggle input tambahan berdasar type (opsional, kalau mau munculkan field media dsb)
  function toggleByType(prefix) {
    const type = document.getElementById(`${prefix}_type`).value;
    const note = document.getElementById(`${prefix}_type_note`);
    note.innerText = (type === 'text') ? 'Balasan berupa teks biasa.' : 'Balasan akan mengirim media sesuai tipe.';
  }
  ['create','edit'].forEach(m => {
    const sel = document.getElementById(`${m}_type`);
    sel && sel.addEventListener('change', () => toggleByType(m));
    sel && toggleByType(m);
  });
});
</script>
@endpush
