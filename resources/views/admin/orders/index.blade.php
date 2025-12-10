@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Pesanan</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Semua Transaksi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Nama Customer</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->table_number }}</td>
                        <td>Rp {{ number_format($order->total_price) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d M H:i') }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-primary">Detail</a>
                            @if ($order->status == 'pending')
                            <button class="btn btn-sm btn-info">Proses</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection