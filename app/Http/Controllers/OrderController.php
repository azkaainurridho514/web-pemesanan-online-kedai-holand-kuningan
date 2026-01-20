<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\OrderEvent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('table_number', 'like', "%{$search}%")
                ->orWhere('order_code', 'like', "%{$search}%");
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
        if (in_array($request->status, ['selesai', 'batal'])) {
            $order->completed_at = now();
        } else {
            $order->completed_at = null;
        }
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


    public function reportView(){
        return view('dashboard.order.report');
    }
    
    public function dataReport(Request $request)
    {
           $status = $request->query('status'); 
    $dateFilter = $request->query('date_filter'); 
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    if (!in_array($status, ['selesai', 'batal'])) {
        return response()->json([
            'message' => 'Status tidak valid. Gunakan ?status=selesai atau ?status=batal'
        ], 400);
    }

    $query = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('orders.status', $status)
        ->select(
            'products.name as product_name',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
        ->groupBy('products.name')
        ->orderByDesc('total_sold');

    if (!empty($dateFilter)) {
        if ($dateFilter === 'today') {
            $query->whereDate('orders.created_at', Carbon::today());
        } elseif ($dateFilter === '7days') {
            $query->where('orders.created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($dateFilter === '30days') {
            $query->where('orders.created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($dateFilter === 'range' && $startDate && $endDate) {
            $query->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }
    }

    $summary = (clone $query)->get();
    $totals = [
        'total_items_sold' => $summary->sum('total_sold'),
        'total_revenue' => $summary->sum('total_revenue'),
    ];

    $reports = $query->paginate(10);

    return response()->json([
        'status' => $status,
        'summary' => $totals,
        'data' => $reports,
    ]);
    }

}
