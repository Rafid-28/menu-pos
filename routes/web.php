<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Halaman Customer (Menu Digital)
Route::get('/', [OrderController::class, 'index'])->name('menu');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');

// Halaman Admin (Kasir)
Route::prefix('admin')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Manage Orders
    Route::put('/orders/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.order.update');
    
    // Manage Categories
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('admin.category.destroy');
    
    // Manage Products
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.product.store');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('admin.product.destroy');

    // routes/web.php
    Route::get('/order/status/{order_number}', function($order_number) {
        // Tampilkan pesan terima kasih atau status pesanan
        $order = \App\Models\Order::where('order_number', $order_number)->first();
        return view('order_status', compact('order'));
    })->name('order.status');
});

// routes/web.php
Route::post('/midtrans-callback', [OrderController::class, 'callback']);