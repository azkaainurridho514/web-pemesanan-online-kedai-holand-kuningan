<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function getData(){
        $data = Category::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
