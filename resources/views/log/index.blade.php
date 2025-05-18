@extends('vendor.layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/plugin.css') }}">
@endpush

@section('content')
    <div class="container mt-5">
        <h3>LOG REALTIME SERVER</h3>

        <div class="card mt-4">
            <div class="card-body p-0">
                <iframe src="http://localhost:5000/log-viewer.html" width="100%" height="600" frameborder="0"
                    style="border: none; background: #111; color: #0f0;">
                </iframe>
            </div>
        </div>
    </div>
@endsection
