<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info">Produk Terlaris</h6>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @forelse ($topProducts as $product)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $loop->iteration }}. {{ $product->name }}
                    <span class="badge badge-info badge-pill">{{ $product->total_quantity }} Terjual</span>
                </li>
            @empty
                <p class="text-center text-muted m-0">Belum ada data produk terlaris.</p>
            @endforelse
        </ul>
    </div>
</div>