<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getData(Request $request){
        $search = $request->search;
        $category = $request->category;

        $query = Product::query()->with('category');
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        if (!empty($category)) {
            $query->where('category_id', $category);
        }
        $data = $query->latest()->get()->map(function ($item) {
            $item->price = price_format($item->price);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
