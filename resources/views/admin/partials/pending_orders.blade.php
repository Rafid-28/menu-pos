<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">Pesanan Pending ({{ $pendingOrdersCount ?? 0 }})</h6>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse ($pendingOrders as $order)
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">{{ $order->order_number }}</small><br>
                        <strong>{{ $order->customer_name }} (Meja {{ $order->table_number }})</strong>
                    </div>
                    <span class="badge badge-warning badge-pill">
                        Rp {{ number_format($order->total_price) }}
                    </span>
                </a>
            @empty
                <p class="text-center text-muted m-0">Tidak ada pesanan pending saat ini.</p>
            @endforelse
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('admin.dashboard', ['filter' => 'pending']) }}" class="btn btn-sm btn-outline-warning">Lihat Semua Pending</a>
        </div>
    </div>
</div>