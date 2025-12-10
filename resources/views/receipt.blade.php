@extends('layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h3>Terima Kasih, Pesanan Berhasil Dibuat!</h3>
            </div>
            <div class="card-body">
                <h5 class="card-title">Struk Pembayaran</h5>
                <hr>

                @if($order->status == 'paid')
                    <div class="alert alert-success text-center">PEMBAYARAN SUDAH DITERIMA!</div>
                @elseif($transaction_status == 'capture' || $transaction_status == 'settlement')
                    <div class="alert alert-success text-center">PEMBAYARAN SUKSES DARI MIDTRANS</div>
                @elseif($transaction_status == 'pending')
                    <div class="alert alert-warning text-center">MENUNGGU PEMBAYARAN (PENDING)</div>
                @else
                    <div class="alert alert-info text-center">STATUS: {{ strtoupper($order->status) }}</div>
                @endif
                
                <p><strong>Order ID:</strong> {{ $order->order_number }}</p>
                <p><strong>Nama Pemesan:</strong> {{ $order->customer_name }}</p>
                <p><strong>No. Meja:</strong> {{ $order->table_number }}</p>
                <hr>
                
                <h6>Detail Pesanan:</h6>
                <ul class="list-group mb-3">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <strong>Rp {{ number_format($item->price * $item->quantity) }}</strong>
                        </li>
                    @endforeach
                </ul>

                <h4>Total Harga: Rp {{ number_format($order->total_price) }}</h4>

                <p class="mt-4 text-center">
                    <a href="/" class="btn btn-primary">Pesan Lagi</a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Lihat Status Kasir</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection