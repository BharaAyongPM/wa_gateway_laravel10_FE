@extends('vendor.layout.master')
@section('title','Dashboard')

@section('content')
<div class="container-fluid">
  <h4 class="mb-3">Dashboard</h4>

  <div class="row">
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Device</div>
        <div class="h5 mb-0">{{ $devices->count() }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Pesan Terkirim</div>
        <div class="h5 mb-0">{{ $totalSent }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Quota Berjalan</div>
        <div class="h5 mb-0">{{ number_format($totalQuotaRemaining,0,',','.') }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">Paket Hampir Habis</div>
        <div class="h5 mb-0">{{ $almostOut }}</div>
        <small class="text-muted d-block">Quota rendah atau exp ≤ 3 hari</small>
      </div></div>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header"><strong>Ringkasan Device</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Device</th>
            <th>Plan</th>
            <th>Remaining Quota</th>
            <th>Expired</th>
            <th>Status</th>
            <th>Trial</th>
          </tr>
        </thead>
        <tbody>
          @forelse($deviceSummaries as $i => $d)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $d['name'] }}</td>
              <td>{{ $d['plan'] ?? '-' }}</td>
              <td>{{ $d['remaining_quota'] !== null ? number_format($d['remaining_quota'],0,',','.') : '-' }}</td>
              <td>{{ $d['expired_at'] ? \Carbon\Carbon::parse($d['expired_at'])->format('d M Y') : '-' }}</td>
              <td>
                @php
                  $map = ['online'=>'success','connected'=>'success','offline'=>'secondary','error'=>'danger'];
                  $badge = $map[$d['status']] ?? 'light';
                @endphp
                <span class="badge badge-{{ $badge }}">{{ strtoupper($d['status'] ?? '-') }}</span>
              </td>
              <td>
                @if($d['is_trial'])
                  <span class="badge badge-info">TRIAL</span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">Belum ada device.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
