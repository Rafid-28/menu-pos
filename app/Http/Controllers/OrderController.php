<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $products = Product::where('is_available', true)->with('category')->get();
        return view('menu', compact('products'));
    }

    public function checkout(Request $request)
    {
        // Request berupa JSON: { customer_name, table_number, items: [{id, qty}] }
        
        $total = 0;
        $orderNumber = 'ORD-' . time() . rand(100,999);

        // 1. Buat Order
        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'total_price' => 0, // hitung nanti
            'status' => 'pending',
        ]);

        // 2. Simpan Item & Hitung Total
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'price' => $product->price,
            ]);
        }

        $order->update(['total_price' => $total]);
        $ngrok_url = env('APP_URL');

        // 3. Request Snap Token ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
            ],
            'callbacks' => [
                'finish' => $ngrok_url . '/order/status/' . $orderNumber,
                'error' => $ngrok_url . '/order/status/' . $orderNumber, // Redirect ke status juga
                'unfinish' => $ngrok_url . '/order/status/' . $orderNumber, // Redirect ke status juga
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
            
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // WEBHOOK HANDLER
    // OrderController.php
    public function callback(Request $request)
    {
        // Ambil Server Key dari .env
        $serverKey = env('MIDTRANS_SERVER_KEY'); 
        
        // Cek Signature Key (Verifikasi Keaslian Notifikasi)
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if ($hashed == $request->signature_key) {
            // Cek Status Transaksi
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                
                // Cari Order di Database berdasarkan Order ID
                $order = Order::where('order_number', $request->order_id)->first();
                
                // Lakukan Update jika ditemukan
                if ($order && $order->status != 'paid') { // Hanya update jika status belum paid
                    $order->update(['status' => 'paid']);
                }
            }
            // Pastikan selalu merespon OK ke Midtrans
            return response('OK', 200); 
        }
        return response('Invalid Signature', 403);
    }
}