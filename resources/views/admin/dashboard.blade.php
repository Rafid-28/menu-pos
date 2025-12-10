@extends('layouts.admin') {{-- Asumsikan Anda punya layout admin --}}

@section('content')
<div class="container-fluid">
    {{-- Header Dashboard --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span class="text-secondary">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
    </div>
    @include('admin.partials.stats')

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            {{-- Grafik Penjualan 7 Hari Terakhir --}}
            @include('admin.partials.sales_chart')
        </div>
        <div class="col-xl-4 col-lg-5">
            {{-- Total Pesanan Pending --}}
            @include('admin.partials.pending_orders')
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4">
            {{-- Pesanan Terbaru --}}
            @include('admin.partials.recent_orders')
        </div>
        <div class="col-lg-6 mb-4">
            {{-- Produk Terlaris --}}
            @include('admin.partials.top_products')
        </div>
    </div>

</div>
@endsection