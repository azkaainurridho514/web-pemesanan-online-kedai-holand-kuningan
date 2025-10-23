<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    protected $cookieName = 'cart';
    protected $cookieTime = 120;

    public function view()
    {
        return view('cart');
    }

   
    public function getData(Request $request)
    {
        $cookieData = json_decode($request->cookie($this->cookieName), true) ?? [];
        $activeCart = collect($cookieData)->firstWhere('is_order', false);
        return response()->json($activeCart ?? ['is_order' => false, 'cart' => []]);
    }

    
    public function addOrUpdate(Request $request)
    {
        $cookieData = json_decode($request->cookie($this->cookieName), true) ?? [];

        $activeCartIndex = collect($cookieData)->search(fn($c) => $c['is_order'] === false);
        if ($activeCartIndex === false) {
            $activeCartIndex = count($cookieData);
            $cookieData[] = ['is_order' => false, 'cart' => []];
        }

        $cart = &$cookieData[$activeCartIndex]['cart'];

        $newItem = [
            'product_id' => $request->product_id,
            'nama' => $request->nama,
            'qty' => (int) $request->qty,
            'harga' => (int) $request->harga,
            'desc' => $request->desc ?? "",
        ];

        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] == $newItem['product_id']) {
                $item['qty'] += $newItem['qty'];
                if (!empty($newItem['desc'])) {
                    $item['desc'] = $newItem['desc'];
                }
                $item['harga'] = $newItem['harga'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = $newItem;
        }

        $cookie = cookie($this->cookieName, json_encode($cookieData), $this->cookieTime);
        return response()->json([
            'message' => $found ? 'Item diperbarui di cart' : 'Item ditambahkan ke cart',
            'cart' => $cart,
        ])->cookie($cookie);
    }

    
    public function remove(Request $request, $id)
    {
        $cookieData = json_decode($request->cookie($this->cookieName), true) ?? [];

        $activeCartIndex = collect($cookieData)->search(fn($c) => $c['is_order'] === false);
        if ($activeCartIndex === false) {
            return response()->json(['message' => 'Cart kosong'], 400);
        }

        $cookieData[$activeCartIndex]['cart'] = array_values(array_filter(
            $cookieData[$activeCartIndex]['cart'],
            fn($item) => $item['product_id'] != $id
        ));

        $cookie = cookie($this->cookieName, json_encode($cookieData), $this->cookieTime);
        return response()->json([
            'message' => 'Item dihapus dari keranjang',
            'cart' => $cookieData[$activeCartIndex]['cart']
        ])->cookie($cookie);
    }

    
    public function clear(Request $request)
    {
        $cookieData = json_decode($request->cookie($this->cookieName), true) ?? [];
        $cookieData = array_filter($cookieData, fn($c) => $c['is_order'] === true); // sisakan history

        $cookieData[] = ['is_order' => false, 'cart' => []];

        $cookie = cookie($this->cookieName, json_encode(array_values($cookieData)), $this->cookieTime);
        return response()->json(['message' => 'Cart dikosongkan'])->cookie($cookie);
    }

    
    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_number' => 'required|string|max:20',
        ]);

        $cookieData = json_decode($request->cookie($this->cookieName), true) ?? [];

        $activeCartIndex = collect($cookieData)->search(fn($c) => $c['is_order'] === false);
        if ($activeCartIndex === false) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }

        $cart = $cookieData[$activeCartIndex]['cart'];
        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }

        $totalPrice = 0;
        foreach ($cart as $item) {
            $harga = (int) preg_replace('/[^0-9]/', '', $item['harga']);
            $totalPrice += $harga * (int) $item['qty'];
        }

        $order = Order::create([
            'id' => Str::uuid(),
            'order_code' => 'ORD-' . strtoupper(Str::random(6)),
            'name' => $request->name,
            'phone' => $request->phone,
            'table_number' => $request->table_number,
            'total_price' => $totalPrice,
            'payment_method' => "cash",
            'status' => 'menunggu',
        ]);

        foreach ($cart as $item) {
            $harga = (int) preg_replace('/[^0-9]/', '', $item['harga']);
            $subtotal = $harga * (int) $item['qty'];
            OrderItem::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['qty'],
                'subtotal' => $subtotal,
                'note' => $item['desc'] ?? "",
            ]);
        }

        $cookieData[$activeCartIndex]['is_order'] = true;
        $cookieData[$activeCartIndex]['date'] = now()->format('Y-m-d H:i:s');
        $cookieData[$activeCartIndex]['order_code'] = $order->order_code;

        $cookieData[] = ['is_order' => false, 'cart' => []];

        $cookie = cookie($this->cookieName, json_encode($cookieData), $this->cookieTime);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat!',
            'order_code' => $order->order_code,
            'total' => $totalPrice,
            'date' => $cookieData[$activeCartIndex]['date'] 
        ])->cookie($cookie);
    }

    
    public function getHistory(Request $request)
    {
        $cookieData = json_decode($request->cookie($this->cookieName), true);

        if (!is_array($cookieData)) {
            return response()->json([]);
        }

        $history = collect($cookieData)
            ->where('is_order', true)
            ->map(function ($order) {
                $cart = $order['cart'] ?? [];

                $count = count($cart);
                $count_item = collect($cart)->sum(function ($item) {
                    return (int) preg_replace('/\D/', '', (string) ($item['qty'] ?? 0));
                });
                $total = collect($cart)->sum(function ($item) {
                    $harga = (int) preg_replace('/[^0-9]/', '', $item['harga']);
                    return $harga * (int) $item['qty'];
                });

                return [
                    'is_order'   => true,
                    'count'      => $count,
                    'count_item' => $count_item,
                    'total'      => $total,
                    'order_code' => $order['order_code'] ?? '',
                    'date'       => $order['date'] ?? '', 
                ];
            })
            ->sortByDesc('date')
            ->values()
            ->toArray();

        return response()->json($history);
    }


}
