@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <h4 class="mb-4">Selamat Datang, Admin</h4>

    <div class="row">
        {{-- Total User --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-account-multiple text-primary icon-lg"></i>
                        <div class="text-right">
                            <p class="mb-0">Total Pengguna</p>
                            <h3 class="font-weight-medium">{{ $totalUser }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3"><i class="mdi mdi-account mr-1"></i> Termasuk semua role</p>
                </div>
            </div>
        </div>

        {{-- User Trial --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-timer-sand text-warning icon-lg"></i>
                        <div class="text-right">
                            <p class="mb-0">Pengguna Trial</p>
                            <h3 class="font-weight-medium">{{ $userTrial }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3"><i class="mdi mdi-clock-fast mr-1"></i> Masa aktif terbatas</p>
                </div>
            </div>
        </div>

        {{-- Pendapatan --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-currency-usd text-success icon-lg"></i>
                        <div class="text-right">
                            <p class="mb-0">Total Pendapatan</p>
                            <h3 class="font-weight-medium">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3"><i class="mdi mdi-cash-multiple mr-1"></i> Seluruh waktu</p>
                </div>
            </div>
        </div>

        {{-- Paket Akan Habis --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                        <div class="text-right">
                            <p class="mb-0">Paket Hampir Habis</p>
                            <h3 class="font-weight-medium">{{ $expiringSoon }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3"><i class="mdi mdi-calendar-clock mr-1"></i> H-3 hari dari expired</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Pengguna per Paket --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Distribusi Pengguna per Paket</h4>
                    <canvas id="chart-paket" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('admin/assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
    const ctx = document.getElementById('chart-paket').getContext('2d');
    const chartPaket = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($planLabels) !!},
            datasets: [{
                label: 'Jumlah Pengguna',
                data: {!! json_encode($planCounts) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>
@endpush
{{-- <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                        @endif --}}