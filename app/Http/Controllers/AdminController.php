<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // --- DASHBOARD & ORDER ---
    public function index()
    {
        $orders = Order::with('items.product')->latest()->get();
        $categories = Category::all();
        $products = Product::with('category')->get();
        
        return view('admin.dashboard', compact('orders', 'categories', 'products'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
            'cashier_note' => $request->cashier_note
        ]);
        return back()->with('success', 'Order updated!');
    }

    // --- CRUD CATEGORY ---
    public function storeCategory(Request $request)
    {
        Category::create($request->only('name'));
        return back()->with('success', 'Kategori ditambahkan');
    }

    public function destroyCategory($id)
    {
        Category::destroy($id);
        return back();
    }

    // --- CRUD PRODUCT ---
    public function storeProduct(Request $request)
    {
        // Simplifikasi upload gambar: simpan nama file saja atau null
        $data = $request->all();
        // $data['image'] = $request->file('image')->store('products'); // Uncomment jika pakai storage
        
        Product::create($data);
        return back()->with('success', 'Menu ditambahkan');
    }

    public function destroyProduct($id)
    {
        Product::destroy($id);
        return back();
    }
}