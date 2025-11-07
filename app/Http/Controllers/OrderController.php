<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\OrderEvent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OrderLog;


class OrderController extends Controller
{
    public function sendOrderEvent(Request $request)
    {
        broadcast(new OrderEvent('info', $request->title, $request->message));
        return response()->json(['status' => 'ok']);
    }
    public function orderView(){
        return view('dashboard.order.order');
    }
    public function cashierView(){
        return view('dashboard.order.cashier');
    }
    public function dataOrderInfo(){
        $orders = Order::orderBy('created_at', 'desc')->get()->groupBy('status');
        return response()->json([
            "wait" => $orders->get('menunggu') ?? [],
            "process" => $orders->get('diproses') ?? [],
            "serve" => $orders->get('dihidangkan') ?? [],
            "done" => $orders->get('selesai') ?? [],
            "cancel" => $orders->get('batal') ?? [],
        ]);
    }
    public function dataOrder(Request $request){
        $search = $request->query('search');
        $status = $request->query('status');
        $query = Order::query();
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('table_number', 'like', "%{$search}%")
                ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('orderItems.product.option.items')->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        OrderLog::create([
            'id' => Str::uuid(),
            'order_id' => $order->id,
            'status' => $request->status,
            'message' => "Status order diperbarui menjadi '{$request->status}'."
        ]);
        return response()->json(['message' => 'Status berhasil diperbarui']);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_number' => 'required|string|max:20',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ]);

        $total = 0;

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->price * $item['qty'];
        }

        $order = Order::create([
            'id' => Str::uuid(),
            'order_code' => 'ORD-' . strtoupper(Str::random(6)),
            'name' => $request->name,
            'phone' => $request->phone,
            'table_number' => $request->table_number,
            'total_price' => $total,
            'payment_method' => 'cash',
            'status' => 'menunggu',
        ]);
        OrderLog::create([
            'id' => Str::uuid(),
            'order_id' => $order->id,
            'status' => 'menunggu',
            'message' => 'Order dibuat dan menunggu konfirmasi.'
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            OrderItem::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'subtotal' => $product->price * $item['qty'],
                'note' => $item['note'] ?? '',
            ]);
        }

        return response()->json([
            'message' => 'Pesanan berhasil dibuat!',
            'order_code' => $order->order_code,
            'total' => $total,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_number' => 'required|string|max:20',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        $order->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'table_number' => $request->table_number,
        ]);

        $order->orderItems()->delete();

        $totalBaru = 0;

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);

            OrderItem::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'subtotal' => $product->price * $item['qty'],
                'note' => $item['note'] ?? '',
            ]);

            $totalBaru += $product->price * $item['qty'];
        }

        $order->update([
            'total_price' => $totalBaru,
        ]);

        OrderLog::create([
            'id' => Str::uuid(),
            'order_id' => $order->id,
            'status' => $order->status,
            'message' => "Produk pada order diperbarui oleh admin."
        ]);

        return response()->json([
            'message' => 'Produk pada order berhasil diganti!',
            'order_code' => $order->order_code,
            'total_baru' => $order->total_price,
        ]);
    }    
}
