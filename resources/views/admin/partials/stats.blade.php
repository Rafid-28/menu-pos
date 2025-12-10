<div class="row">

    {{-- Penjualan Hari Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            PENJUALAN HARI INI
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($salesToday) }}</div>
                        <div class="text-xs mt-1 text-success">
                            <i class="fas fa-arrow-up"></i> {{ $salesGrowth }}% dari kemarin
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Pesanan --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            TOTAL PESANAN
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                        <div class="text-xs mt-1 text-info">
                            <i class="fas fa-arrow-up"></i> 5% dari periode lalu
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pesanan Pending --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            PESANAN PENDING
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingOrdersCount }}</div>
                        <div class="text-xs mt-1 text-warning">
                            A Perlu perhatian
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tambahkan Penjualan Bulan Ini jika diperlukan --}}

</div>