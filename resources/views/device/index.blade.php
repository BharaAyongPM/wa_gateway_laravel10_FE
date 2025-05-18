@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <div class="container mt-5">
        <h3>Manajemen Device WhatsApp</h3>
        <form action="{{ route('device.create') }}" method="POST">
            @csrf
            <button class="btn btn-success mb-3">Tambah Device</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Last Connected</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $device)
                    <tr>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->status }}</td>
                        <td>{{ $device->last_connected_at ?? '-' }}</td>
                        <td>
                            <a href="{{ route('device.qr', $device->id) }}" class="btn btn-primary btn-sm">QR</a>
                            <button class="btn btn-sm btn-warning" onclick="showApiKeyModal({{ $device->id }})">API
                                Key</button>
                            <a href="{{ route('device.status', $device->id) }}" class="btn btn-info btn-sm">Cek Status</a>
                            <form action="{{ route('device.destroy', $device->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="apiKeyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">API Key Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="apiKeyValue" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyApiKey()">Salin</button>
                    </div>
                    <small class="text-muted mt-2 d-block">Gunakan API Key ini untuk integrasi aplikasi eksternal.</small>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showApiKeyModal(deviceId) {
            fetch(`/device/${deviceId}/generate-apikey`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("apiKeyValue").value = data.api_key;
                    new bootstrap.Modal(document.getElementById('apiKeyModal')).show();
                })
                .catch(err => alert("Gagal mengambil API Key"));
        }

        function copyApiKey() {
            const input = document.getElementById("apiKeyValue");
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            alert("API Key berhasil disalin!");
        }
    </script>
    {{-- @if (isset($device))
        <script>
            setInterval(() => {
                fetch("{{ route('device.status', $device->id) }}")
                    .then(() => location.reload());
            }, 15000);
        </script>
    @endif --}}
@endsection
