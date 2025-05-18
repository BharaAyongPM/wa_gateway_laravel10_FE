@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div
                        class="d-flex flex-md-column flex-xl-row flex-wrap justify-content-between align-items-md-center justify-content-xl-between">
                        <div class="float-left">
                            <i class="mdi mdi-cube text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Total Venue</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">#</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 text-left text-md-center text-xl-left">
                        <i class="mdi mdi-alert-octagon mr-1" aria-hidden="true"></i> total venue anda
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div
                        class="d-flex flex-md-column flex-xl-row flex-wrap justify-content-between align-items-md-center justify-content-xl-between">
                        <div class="float-left">
                            <i class="mdi mdi-receipt text-warning icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">total</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">#</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 text-left text-md-center text-xl-left">
                        <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> total semua order
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div
                        class="d-flex flex-md-column flex-xl-row flex-wrap justify-content-between align-items-md-center justify-content-xl-between">
                        <div class="float-left">
                            <i class="mdi mdi-poll-box text-success icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Total pendapatan</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">Rp.
                                    #</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 text-left text-md-center text-xl-left">
                        <i class="mdi mdi-calendar mr-1" aria-hidden="true"></i> pendapatan anda
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div
                        class="d-flex flex-md-column flex-xl-row flex-wrap justify-content-between align-items-md-center justify-content-xl-between">
                        <div class="float-left">
                            <i class="mdi mdi-account-box-multiple text-info icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Siap Widthdraw</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">Rp.
                                    #</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 text-left text-md-center text-xl-left">
                        <i class="mdi mdi-reload mr-1" aria-hidden="true"></i> Dana siap di tarik
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title mb-0">Analisis Venue</h2>
                        <div class="wrapper d-flex">
                            <div class="d-flex align-items-center mr-3">
                                <span class="dot-indicator bg-success"></span>
                                <p class="mb-0 ml-2 text-muted">Pesanan Sukses</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="dot-indicator bg-primary"></span>
                                <p class="mb-0 ml-2 text-muted">Pesanan Batal</p>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="dashboard-area-chart1" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengumuman</h4>
                    <div class="add-items d-flex">
                        {{-- <input type="text" class="form-control todo-list-input"
                            placeholder="What do you need to do today?">
                        <button class="add btn btn-primary font-weight-medium todo-list-add-btn">Add</button> --}}
                    </div>
                    <div class="announcement-list">
                        <div class="alert alert-info" role="alert">
                            <strong>Pengumuman 1:</strong> Jadwal maintenance pada tanggal 25 April 2025.
                        </div>
                        <div class="alert alert-success" role="alert">
                            <strong>Pengumuman 2:</strong> Promo diskon 20% untuk semua venue hingga akhir bulan.
                        </div>
                        <div class="alert alert-warning" role="alert">
                            <strong>Pengumuman 3:</strong> Harap perbarui informasi akun Anda sebelum 30 April 2025.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Venue Menunggu Pembayaran</h4>
                    <div class="venue-list d-flex align-items-center justify-content-between mb-3">
                        <h3>22 April 2025</h3>
                        <small>3 Venue</small>
                    </div>
                    <div class="venue border-bottom py-3">
                        <p class="mb-2 font-weight-medium">Venue A - Gedung Serbaguna</p>
                        <div class="d-flex align-items-center">
                            <div class="badge badge-warning">Rp. 5.000.000</div>
                            <small class="text-muted ml-2">Batas Pembayaran: 23 April 2025</small>
                            <div class="ml-auto">
                                <button class="btn btn-sm btn-warning">PENDING</button>
                            </div>
                        </div>
                    </div>
                    <div class="venue py-3 border-bottom">
                        <p class="mb-2 font-weight-medium">Venue B - Aula Kampus</p>
                        <div class="d-flex align-items-center">
                            <div class="badge badge-warning">Rp. 3.500.000</div>
                            <small class="text-muted ml-2">Batas Pembayaran: 24 April 2025</small>
                            <div class="ml-auto">
                                <button class="btn btn-sm btn-warning">PENDING</button>
                            </div>
                        </div>
                    </div>
                    <div class="venue py-3">
                        <p class="mb-2 font-weight-medium">Venue C - Lapangan Futsal</p>
                        <div class="d-flex align-items-center">
                            <div class="badge badge-warning">Rp. 2.000.000</div>
                            <small class="text-muted ml-2">Batas Pembayaran: 25 April 2025</small>
                            <div class="ml-auto">
                                <button class="btn btn-sm btn-warning">PENDING</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4 grid-margin stretch-card">
            <div class="row flex-grow">
                <div class="col-md-6 col-xl-12 grid-margin grid-margin-md-0 grid-margin-xl stretch-card">
                    <div class="card card-revenue">
                        <div class="card-body d-flex align-items-center">
                            <div class="d-flex flex-grow">
                                <div class="mr-auto">
                                    <p class="highlight-text mb-0 text-white"> Rp. 5.000.0000 </p>
                                    <p class="text-white"> Bulan ini </p>
                                    <div class="badge badge-pill"> Mantap </div>
                                </div>
                                <div class="ml-auto align-self-end">
                                    <div id="revenue-chart" sparkType="bar" sparkBarColor="#e6ecf5" barWidth="2">
                                        4,3,10,9,4,3,8,6,7,8 </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-12 stretch-card">
                    <div class="card card-revenue-table">
                        <div class="card-body">
                            <div class="revenue-item d-flex">
                                <div class="revenue-desc">
                                    <h6>Member Profit</h6>
                                    <p class="font-weight-light"> Average Weekly Profit </p>
                                </div>
                                <div class="revenue-amount">
                                    <p class="text-primary"> +168.900 </p>
                                </div>
                            </div>
                            <div class="revenue-item d-flex">
                                <div class="revenue-desc">
                                    <h6>Total Profit</h6>
                                    <p class="font-weight-light"> Weekly Customer Orders </p>
                                </div>
                                <div class="revenue-amount">
                                    <p class="text-primary"> +6890.00 </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('admin/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
@endpush


@push('custom-scripts')
    <script src="{{ asset('admin/assets/js/dashboard.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script>
        const ctx = document.getElementById('dashboard-area-chart1').getContext('2d');
        const areaChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                        label: 'Pesanan Sukses',
                        data: @json($completedData),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pesanan Batal',
                        data: @json($cancelledData),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script> --}}
@endpush
