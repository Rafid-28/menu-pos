<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Order;

// Halaman Customer (Menu Digital)
Route::get('/', [OrderController::class, 'index'])->name('menu');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');

Route::prefix('admin')->group(function() {

    Route::get('/dashboard', function () {
        $salesToday = Order::whereDate('created_at', today())
                           ->where('status', 'paid')
                           ->sum('total_price');
        
        $salesYesterday = Order::whereDate('created_at', now()->subDay())
                               ->where('status', 'paid')
                               ->sum('total_price');
        $salesGrowth = 0;
        if ($salesYesterday > 0) {
            $salesGrowth = round((($salesToday - $salesYesterday) / $salesYesterday) * 100, 1);
        } elseif ($salesToday > 0 && $salesYesterday == 0) {
            $salesGrowth = 100;
        }

        $pendingOrders = Order::where('status', 'pending')->take(5)->get();
        $recentOrders = Order::orderBy('id', 'desc')->take(10)->get();
        $topProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->with('product') // Pastikan relasi ke Product ada di OrderItem model
            ->get()
            ->map(function ($item) {
                $item->name = $item->product->name ?? 'Produk Dihapus';
                return $item;
            });


        $totalOrders = Order::count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();
    
    return view('admin.dashboard', [
            'salesToday' => $salesToday,
            'totalOrders' => $totalOrders,
            'pendingOrdersCount' => $pendingOrdersCount,
            'salesGrowth' => $salesGrowth, // <<< VARIABEL BARU DIKIRIM!
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    })->name('admin.dashboard');

    Route::get('/products', [AdminController::class, 'productsIndex'])->name('admin.products.index');
    Route::get('/categories', [AdminController::class, 'categoriesIndex'])->name('admin.categories.index');

    // --- Manajemen Pesanan ---
    Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
    Route::get('/orders/create', [AdminController::class, 'orderCreate'])->name('admin.orders.create'); // "Buat Pesanan"
    // Manage Orders
    Route::put('/orders/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.order.update');
    
    // Manage Categories
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('admin.category.destroy');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.category.update');
    
    // Manage Products
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.product.store');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('admin.product.destroy');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('admin.product.update');

    // routes/web.php
    Route::get('/order/status/{order_number}', function($order_number) {
        // Tampilkan pesan terima kasih atau status pesanan
        $order = \App\Models\Order::where('order_number', $order_number)->first();
        return view('order_status', compact('order'));
    })->name('order.status');

});

// routes/web.php
Route::get('/order/receipt/{order_number}', [OrderController::class, 'showReceipt'])->name('order.receipt');
Route::post('/midtrans-callback', [OrderController::class, 'callback']);