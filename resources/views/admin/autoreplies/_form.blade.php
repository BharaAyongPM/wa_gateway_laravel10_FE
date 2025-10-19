@php $p = $mode==='edit' ? 'edit_' : 'create_'; @endphp

<div class="form-group">
  <label>Keyword</label>
  <input type="text" id="{{ $p }}keyword" name="keyword" class="form-control" required>
  <small class="text-muted">Contoh: "halo", "cek resi", "help"</small>
</div>

<div class="form-group">
  <label>Response</label>
  <textarea id="{{ $p }}response" name="response" class="form-control" rows="3" required></textarea>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Tipe Balasan</label>
    <select id="{{ $p }}type" name="type" class="form-control" required>
      <option value="text">Text</option>
      <option value="image">Image</option>
      <option value="pdf">PDF</option>
      <option value="document">Document</option>
    </select>
    <small id="{{ $p }}type_note" class="text-muted"></small>
  </div>
  <div class="form-group col-md-6">
    <label>Device</label>
    <select id="{{ $p }}device_id" name="device_id" class="form-control">
      <option value="">— Pilih Device —</option>
      @foreach($devices as $d)
        <option value="{{ $d->id }}">{{ $d->name }}</option>
      @endforeach
    </select>
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>User (opsional)</label>
    <input type="number" id="{{ $p }}user_id" name="user_id" class="form-control" placeholder="User ID (opsional)">
  </div>
  <div class="form-group col-md-6 d-flex align-items-center">
    <div class="custom-control custom-switch mt-3">
      <input type="checkbox" id="{{ $p }}active" name="active" class="custom-control-input" value="1" {{ $mode==='create'?'checked':'' }}>
      <label class="custom-control-label" for="{{ $p }}active">Active</label>
    </div>
  </div>
</div>
