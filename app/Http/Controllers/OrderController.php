<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log; // Digunakan untuk debugging

class OrderController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); // false untuk Sandbox
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
        try {
            // 1. INISIALISASI VARIABEL AWAL
            $orderNumber = 'ORD-' . time() . rand(100,999);
            $total = 0;
            $items = $request->items; // Item dari AJAX

            // 2. HITUNG TOTAL HARGA
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                if (!$product) { continue; }
                $total += $product->price * $item['qty'];
            }

            // Validasi Total Harga
            if ($total <= 1000) {
                 return response()->json(['error' => 'Total harga harus minimal Rp 1.000.'], 400);
            }

            // 3. BUAT ORDER UTAMA DI DATABASE
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'table_number' => $request->table_number,
                'total_price' => $total,
                'status' => 'pending',
            ]);
            
            // 4. SIMPAN ORDER ITEM KE DATABASE
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                if (!$product) { continue; }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                ]);
            }
            
            // 5. SIAPKAN PARAMETER MIDTRANS SNAP
            $ngrok_url = env('APP_URL'); // Mengambil URL Ngrok dari .env

            $params = [
                'transaction_details' => [
                    'order_id' => $orderNumber,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                ],
                // Callbacks untuk redirect setelah pembayaran
                'callbacks' => [
                    'finish' => $ngrok_url . '/order/receipt/' . $orderNumber, 
                    'error' => $ngrok_url . '/order/receipt/' . $orderNumber,
                    'unfinish' => $ngrok_url . '/order/receipt/' . $orderNumber,
                ]
            ];

            // 6. DAPATKAN SNAP TOKEN
            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            Log::info('Snap Token created successfully', ['order_id' => $orderNumber]);

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            // Log Error Midtrans yang spesifik
            Log::error('Midtrans Snap Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan pada server saat Checkout. Cek log server.'], 500);
        }
    }

    public function callback(Request $request)
    {
        // Ambil Server Key dari .env
        $serverKey = env('MIDTRANS_SERVER_KEY'); 
        
        // Cek Signature Key (Verifikasi Keaslian Notifikasi)
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if ($hashed == $request->signature_key) {
            $transaction_status = $request->transaction_status;
            $order_id = $request->order_id;

            // Status yang menandakan pembayaran sudah berhasil
            if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
                
                // Cari Order di Database berdasarkan Order ID
                $order = Order::where('order_number', $order_id)->first();
                
                // Lakukan Update jika ditemukan
                if ($order && $order->status != 'paid') {
                    $order->update(['status' => 'paid']);
                    Log::info('Order status updated to PAID via Webhook', ['order_id' => $order_id]);
                }
            }
            // Pastikan selalu merespon 200 OK ke Midtrans jika signature valid
            return response('OK', 200); 
        }
        // Signature gagal
        Log::error('Midtrans Signature Mismatch', ['order_id' => $request->order_id]);
        return response('Invalid Signature', 403);
    }

    public function showReceipt($order_number)
    {
        $order = Order::with('items.product')
                        ->where('order_number', $order_number)
                        ->firstOrFail();
        
        // Ambil status dari query parameter (untuk tampilan awal)
        $transaction_status = request('transaction_status');

        return view('receipt', compact('order', 'transaction_status'));
    }
}