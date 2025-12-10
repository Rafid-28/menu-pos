<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosController extends Controller
{
    // Menampilkan Daftar Pesanan
    public function index()
    {
        // Tampilkan order yg 'paid' atau 'pending'
        $orders = Order::with('items.product')->latest()->get(); 
        return view('admin.pos.index', compact('orders'));
    }

    // Update Status & Tambah Pesan Kasir
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status, // misal: 'completed'
            'cashier_note' => $request->cashier_note // Pesan: "Pesanan sedang disiapkan"
        ]);

        return back()->with('success', 'Order updated');
    }
}
