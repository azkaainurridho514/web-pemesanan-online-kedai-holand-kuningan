<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\OrderEvent;
use App\Models\Order;

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
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

    
}
