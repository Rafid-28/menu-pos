<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Penjualan 7 Hari Terakhir</h6>
    </div>
    <div class="card-body">
        <canvas id="salesChart"></canvas>
        <p class="text-center mt-3">Grafik Penjualan akan muncul di sini.</p>
    </div>
</div>

{{-- Untuk mengimplementasikan Chart.js sepenuhnya, Anda perlu menambahkan skrip di bawah ini:
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Logic Chart.js menggunakan data dari backend
</script>
@endpush
--}}