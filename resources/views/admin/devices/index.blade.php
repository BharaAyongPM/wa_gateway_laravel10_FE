@extends('vendor.layout.master')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Data Device</h4>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Nama Device</th>
                        <th>Session ID</th>
                        <th>Status</th>
                        <th>Nama User</th>
                        <th>Terakhir Terhubung</th>
                        <th style="width:220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devices as $i => $device)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $device->name ?? '-' }}</td>
                            <td class="text-monospace">{{ $device->session_id }}</td>
                            <td>
                                @if ($device->status === 'connected')
                                    <span class="badge badge-success">Connected</span>
                                @elseif ($device->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Disconnected</span>
                                @endif
                            </td>
                            <td>{{ $device->user->name ?? '-' }}</td>
                            <td>
                                @if ($device->last_connected_at)
                                    {{ \Carbon\Carbon::parse($device->last_connected_at)->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Aksi">
                                    {{-- Cek Koneksi --}}
                                    <a
                                        href="{{ route('device.status', $device->id) }}"
                                        class="btn btn-sm btn-info"
                                        title="Cek status koneksi terbaru"
                                        onclick="return confirm('Cek status device ini sekarang?')"
                                    >
                                        <i class="mdi mdi-refresh"></i> Cek Koneksi
                                    </a>

                                    {{-- Putuskan Koneksi (hapus device + sesi di server) --}}
                                    <form
                                        action="{{ route('device.destroy', $device->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Putuskan koneksi dan hapus sesi di server WA untuk device ini? Tindakan ini tidak dapat dibatalkan.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-power-plug-off"></i> Putuskan Koneksi
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($devices->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada device.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
