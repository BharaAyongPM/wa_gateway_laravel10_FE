@php $p = $mode==='edit' ? 'edit_' : 'create_'; @endphp

<div class="form-group">
  <label>Pesan</label>
  <textarea id="{{ $p }}message" name="message" class="form-control" rows="3" required></textarea>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Waktu Kirim</label>
    <input type="time" id="{{ $p }}send_time" name="send_time" class="form-control" required>
  </div>
  <div class="form-group col-md-6 d-flex align-items-center">
    <div class="custom-control custom-switch mt-3">
      <input type="checkbox" id="{{ $p }}active" name="active" class="custom-control-input" value="1" {{ $mode==='create'?'checked':'' }}>
      <label class="custom-control-label" for="{{ $p }}active">Active</label>
    </div>
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Device (milik user role admin)</label>
    <select id="{{ $p }}device_id" name="device_id" class="form-control" required>
      <option value="">— Pilih Device —</option>
      @foreach($adminDevices as $d)
        <option value="{{ $d->id }}">{{ $d->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group col-md-6">
    <label>User (role admin)</label>
    <select id="{{ $p }}user_id" name="user_id" class="form-control" required>
      <option value="">— Pilih User —</option>
      @foreach($users as $u)
        @if($u->role==='admin')
          <option value="{{ $u->id }}">{{ $u->name }} (admin)</option>
        @endif
      @endforeach
    </select>
  </div>
</div>

<div class="form-group">
  <label>Daftar Grup/Target (satu per baris)</label>
  <textarea id="{{ $p }}groups" class="form-control" rows="4"
            name="groups"
            oninput="this.name='groups[]'; this.value = this.value;"></textarea>
  <small class="text-muted">Masukkan ID grup / nomor tujuan (62xxxxxxxxxx), satu baris satu target. (Akan disimpan sebagai array)</small>
</div>

<script>
// Ubah textarea ke array saat submit (agar jadi groups[]=x, groups[]=y)
document.currentScript.closest('form')?.addEventListener('submit', function (e) {
  const ta = this.querySelector('#{{ $p }}groups');
  if (!ta) return;
  // bersihkan input groups[] lama
  this.querySelectorAll('input[name="groups[]"]').forEach(n => n.remove());

  const lines = (ta.value || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
  lines.forEach(v => {
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'groups[]';
    inp.value = v;
    this.appendChild(inp);
  });
});
</script>
