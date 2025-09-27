@extends('vendor.layout.master')

@section('title', 'Plans')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="mb-0">Plan Management</h4>
            <small class="text-muted">Kelola paket langganan WA Gateway</small>
        </div>
        <div class="col text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
                <i class="fas fa-plus"></i> Tambah Plan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Durasi (hari)</th>
                        <th>Max Device</th>
                        <th>Quota</th>
                        <th>Fitur</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $i => $plan)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                            <td>{{ $plan->duration }}</td>
                            <td>{{ $plan->max_devices }}</td>
                            <td>{{ $plan->quota_limit }}</td>
                            <td>
                                @if($plan->can_image) <span class="badge badge-info">Image</span> @endif
                                @if($plan->can_pdf) <span class="badge badge-warning">PDF</span> @endif
                                @if($plan->can_autoreply) <span class="badge badge-success">Autoreply</span> @endif
                                @unless($plan->can_image || $plan->can_pdf || $plan->can_autoreply)
                                    <span class="text-muted">-</span>
                                @endunless
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $plan->id }}">
  Edit
</button>

                                <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus plan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">Belum ada data plan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCreateLabel">Tambah Plan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.plans.partials._form', ['mode' => 'create'])
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit (form dirender tetap) --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" id="formEdit" class="modal-content" action="#">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Plan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        @include('admin.plans.partials._form', ['mode' => 'edit'])
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>


@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
  document.body.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-edit');
    if (!btn) return;

    const id = btn.getAttribute('data-id');

    // Pakai URL RELATIF agar tidak kena mixed content
    const fetchUrl  = `/admin/plans/${id}`;
    const updateUrl = `/admin/plans/${id}`;

    try {
      const res = await fetch(fetchUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });
      if (!res.ok) throw new Error('Gagal mengambil data plan.');
      const data = await res.json();

      // Set action ke form edit
      const formEdit = document.getElementById('formEdit');
      formEdit.setAttribute('action', updateUrl);

      // Isi field
      document.getElementById('edit_name').value = data.name ?? '';
      document.getElementById('edit_price').value = data.price ?? 0;
      document.getElementById('edit_duration').value = data.duration ?? 1;
      document.getElementById('edit_max_devices').value = data.max_devices ?? 1;
      document.getElementById('edit_quota_limit').value = data.quota_limit ?? 0;
      document.getElementById('edit_can_image').checked = !!data.can_image;
      document.getElementById('edit_can_pdf').checked   = !!data.can_pdf;
      document.getElementById('edit_can_autoreply').checked = !!data.can_autoreply;

      // Tampilkan modal (BS4 atau BS5)
      const modalEl = document.getElementById('modalEdit');
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        // Bootstrap 5
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
      } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
        // Bootstrap 4 (AdminLTE 3)
        $('#modalEdit').modal('show');
      } else {
        console.warn('Bootstrap modal API tidak ditemukan.');
      }
    } catch (err) {
      console.error(err);
      alert('Tidak bisa membuka modal edit. Coba reload halaman.');
    }
  });
});
</script>


