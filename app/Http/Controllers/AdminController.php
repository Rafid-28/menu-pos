<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $totalSales = Order::where('status', 'paid')->sum('total_amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $categoriesCount = Category::count();
        $productsCount = Product::count();
        
        return view('admin.dashboard', compact('totalSales', 'pendingOrders', 'categoriesCount', 'productsCount'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'paid', 'cancelled'])],
            'cashier_note' => 'nullable|string|max:255'
        ]);
        
        $order->update([
            'status' => $request->status,
            'cashier_note' => $request->cashier_note
        ]);
        
        return back()->with('success', 'Status Pesanan berhasil diperbarui!');
    }

    public function ordersIndex()
    {
        $orders = Order::orderByDesc('id')->paginate(20); 
        return view('admin.orders.index', compact('orders'));
    }

    public function orderCreate()
    {
        $products = Product::where('is_available', true)->get();
        return view('admin.orders.create', compact('products'));
    }
    
    public function categoriesIndex()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($request->only('name'));
        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($id)], 
        ]);

        $category->update($request->only('name'));

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori karena masih memiliki produk terkait.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
    
    public function productsIndex()
    {
        $products = Product::with('category')->get();
        $categories = Category::all(); 
        return view('admin.products.index', compact('products', 'categories'));
    }

public function storeProduct(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id', 
        'price' => 'required|numeric|min:0', 
        'is_available' => 'sometimes', 
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = $request->except('_token');
    $data['is_available'] = $request->has('is_available');
    
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('products', 'public');
        $data['image'] = str_replace('public/', '', $path); 
    }

    \App\Models\Product::create($data); 
    return back()->with('success', 'Produk baru berhasil ditambahkan.');
}

public function updateProduct(Request $request, $id)
{
    $product = \App\Models\Product::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'is_available' => 'sometimes',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    
    $data = $request->except('_token', '_method');
    $data['is_available'] = $request->has('is_available'); 

    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }
        
        $path = $request->file('image')->store('products', 'public');
        $data['image'] = str_replace('public/', '', $path);
    }

    $product->update($data);
    return back()->with('success', 'Produk berhasil diperbarui.');
}

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }
}