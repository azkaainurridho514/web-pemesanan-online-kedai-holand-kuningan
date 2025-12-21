@extends('layout-dashboard.main')
@section('title', "Kedai Holand | Dashboard")    
@section('title-page', "Dashboard")    
@section('main')
<div class="container mt-4">
    <div class="alert alert-light border shadow-sm rounded-3">
        <h5 class="fw-bold mb-1">
            Selamat Datang kembali, {{ auth()->user()->name }}
        </h5>
        <span class="text-secondary">
            Kelola pesanan, menu, dan laporan dengan mudah melalui dashboard ini.
        </span>
    </div>
</div>

@endsection

@push('script')
    
@endpush

