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
        $orders = Order::orderBy('created_at', 'desc')->paginate(15); 
        return response()->json($orders);
        // $orders = Order::latest()->get();
        // return response()->json([
        //     "data" => $orders,
        // ]);
    }
    
}
