@php
    // mode: 'create' | 'edit'
    $prefix = $mode === 'edit' ? 'edit_' : '';
@endphp

<div class="form-group">
    <label>Nama Plan</label>
    <input type="text" id="{{ $prefix }}name" name="name" class="form-control" required>
</div>

<div class="form-group">
    <label>Harga (Rp)</label>
    <input type="number" id="{{ $prefix }}price" name="price" class="form-control" min="0" step="1000" required>
    <small class="text-muted">Contoh: 50000</small>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Durasi (hari)</label>
        <input type="number" id="{{ $prefix }}duration" name="duration" class="form-control" min="1" required>
    </div>
    <div class="form-group col-md-4">
        <label>Maks. Device</label>
        <input type="number" id="{{ $prefix }}max_devices" name="max_devices" class="form-control" min="1" required>
    </div>
    <div class="form-group col-md-4">
        <label>Quota Pesan</label>
        <input type="number" id="{{ $prefix }}quota_limit" name="quota_limit" class="form-control" min="0" required>
    </div>
</div>

<div class="form-group">
    <label class="d-block">Fitur</label>
    <div class="custom-control custom-switch d-inline-block mr-3">
        <input type="checkbox" class="custom-control-input" id="{{ $prefix }}can_image" name="can_image" value="1">
        <label class="custom-control-label" for="{{ $prefix }}can_image">Kirim Gambar</label>
    </div>
    <div class="custom-control custom-switch d-inline-block mr-3">
        <input type="checkbox" class="custom-control-input" id="{{ $prefix }}can_pdf" name="can_pdf" value="1">
        <label class="custom-control-label" for="{{ $prefix }}can_pdf">Kirim PDF</label>
    </div>
    <div class="custom-control custom-switch d-inline-block">
        <input type="checkbox" class="custom-control-input" id="{{ $prefix }}can_autoreply" name="can_autoreply" value="1">
        <label class="custom-control-label" for="{{ $prefix }}can_autoreply">Auto Reply</label>
    </div>
</div>
