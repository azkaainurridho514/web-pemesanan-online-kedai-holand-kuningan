<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
  
    protected $cookieName = 'cart_data';
    protected $cookieTime = 720; 

    public function index(Request $request)
    {
        $cartData = json_decode($request->cookie($this->cookieName), true) ?? [];
        return response()->json($cartData);
    }               

    public function addOrUpdate(Request $request)
    {
        $cart = json_decode($request->cookie($this->cookieName), true) ?? [];

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
                if ($newItem['desc']) {
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

        $cookie = cookie($this->cookieName, json_encode($cart), $this->cookieTime);

        return response()->json([
            'message' => $found ? 'Item diperbarui di cart' : 'Item ditambahkan ke cart',
            'cart' => $cart
        ])->cookie($cookie);
    }

    public function remove(Request $request, $menu_id)
    {
        $cart = json_decode($request->cookie($this->cookieName), true) ?? [];

        $cart = array_filter($cart, fn($item) => $item['menu_id'] != $menu_id);

        $cart = array_values($cart);

        $cookie = cookie($this->cookieName, json_encode($cart), $this->cookieTime);

        return response()->json([
            'message' => 'Item dihapus dari cart',
            'cart' => $cart
        ])->cookie($cookie);
    }


    public function clear()
    {
        $cookie = Cookie::forget($this->cookieName);
        return response()->json(['message' => 'Cart dikosongkan'])->withCookie($cookie);
    }
}
