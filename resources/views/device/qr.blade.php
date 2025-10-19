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
                fetch('/device/{{ $device->id }}/qrcode-live')
                    .then(res => res.json())
                    .then(data => {
                        const el = document.getElementById('qr-container');

                        if (data.connected) {
                            el.innerHTML = `
            <div class="alert alert-success">✅ Perangkat sudah terhubung.</div>
            <a href="{{ route('device.status', $device->id) }}" class="btn btn-success mt-3">Cek Status</a>
          `;
                            return;
                        }

                        if (data.qr) {
                            el.innerHTML = `<img src="${data.qr}" alt="QR" style="max-width: 300px;" />`;
                        } else {
                            el.innerHTML = `<p>⏳ Menunggu QR dari server...</p>`;
                        }
                    })
                    .catch(err => {
                        console.error('Gagal ambil QR:', err);
                        document.getElementById('qr-container').innerHTML = `<p>❌ Gagal memuat QR</p>`;
                    });
            }

            loadQr();
            setInterval(loadQr, 5000); // refresh tiap 3 detik
        </script>


        <a href="{{ route('device.status', $device->id) }}" class="btn btn-success mt-3">Cek Status</a>
        <a href="{{ route('device.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
@endsection
