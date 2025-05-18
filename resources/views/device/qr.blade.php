@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <div class="container mt-5 text-center">
        <h4>Scan QR untuk: {{ $device->name }}</h4>

        <div id="qr-container">
            <p>Memuat QR...</p>
        </div>

        <script>
            function loadQr() {
                fetch('/api/device/{{ $device->id }}/qrcode-live')
                    .then(res => res.json())
                    .then(data => {
                        if (data.qr) {
                            document.getElementById('qr-container').innerHTML =
                                `<img src="${data.qr}" style="max-width: 300px;" />`;
                        }
                    })
                    .catch(err => {
                        console.error('Gagal ambil QR:', err);
                    });
            }

            loadQr(); // load awal
            setInterval(loadQr, 5000); // refresh setiap 5 detik
        </script>

        <a href="{{ route('device.status', $device->id) }}" class="btn btn-success mt-3">Cek Status</a>
        <a href="{{ route('device.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
@endsection
