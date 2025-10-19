@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
<div class="container">
    <h4>Daftar Broadcast</h4>
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#broadcastModal">Tambah Broadcast</button>
   <!-- Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="{{ route('broadcast.store') }}" method="POST">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Broadcast</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label>Pesan</label>
          <textarea name="message" class="form-control" required></textarea>
        </div>
        <div class="mb-2">
          <label>Jam Kirim</label>
          <input type="time" name="send_time" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Device ID</label>
      <select name="device" id="deviceSelect" class="form-control" required>
    <option value="" disabled selected>-- Pilih Device --</option>
    @foreach ($devices as $device)
        <option value="{{ $device->session_id }}">
            {{ $device->name }} ({{ $device->number ?? 'no-number' }}) — {{ $device->session_id }}
        </option>
    @endforeach
</select>


        </div>
        <div class="mb-2">
          <label>Pilih Grup</label>
          <select name="groups[]" id="groupSelect" class="form-control" multiple required>
            <option disabled>-- Pilih grup setelah isi device --</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
    <hr>

    <table class="table table-bordered mt-3">
        <thead>
            <tr><th>Pesan</th><th>Jam</th><th>Device</th><th>Grup</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @foreach ($broadcasts as $b)
                <tr>
                    <td style="white-space: pre-wrap; word-break: break-word; max-width: 300px;">
    {{ $b->message }}
</td>
                    <td>{{ $b->send_time->format('H:i') }}</td>
                    <td>{{ $b->device }}</td>
                    <td>{{ implode(', ', $b->groups) }}</td>
                    <td>{{ $b->active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td class="d-flex gap-1">
    <form method="POST" action="{{ route('broadcast.toggle', $b->id) }}">
        @csrf
        <button class="btn btn-sm btn-warning">Toggle</button>
    </form>

    <form method="POST" action="{{ route('broadcast.destroy', $b->id) }}" onsubmit="return confirm('Yakin ingin menghapus broadcast ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger">Hapus</button>
    </form>
</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('plugin-scripts')
<script>
document.getElementById('deviceSelect').addEventListener('change', function () {
  const deviceId = this.value;
  const groupSelect = document.getElementById('groupSelect');
  groupSelect.innerHTML = `<option disabled>Loading...</option>`;

  fetch(`http://localhost:5000/device/${deviceId}/groups`)
    .then(response => response.json())
    .then(data => {
      groupSelect.innerHTML = '';
      data.groups.forEach(group => {
        const option = document.createElement('option');
        option.value = group.id;
        option.textContent = group.name;
        groupSelect.appendChild(option);
      });
    })
    .catch(error => {
      console.error('❌ Gagal ambil grup:', error);
      groupSelect.innerHTML = `<option disabled>Gagal memuat grup</option>`;
    });
});
</script>
@endpush