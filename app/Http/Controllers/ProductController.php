<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Option;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{
    public function getData(Request $request){
        $search = $request->search;
        $category = $request->category;

        $query = Product::query()->with([
            'category:id,name',
            'option:id,name',
            'option.items:id,option_id,name'
        ])
        ->where('is_available', true); 
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

    public function getDataDashboard(Request $request){
        $query = Product::with(['category:id,name', 'option:id,name'])
        ->select('id', 'category_id', 'option_id', 'name', 'description', 'price', 'photo', 'is_available');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%".$request->search."%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('option_id')) {
            $query->where('option_id', $request->option_id);
        }

        if ($request->filled('is_available')) {
            $isAvailable = $request->is_available == '1';
            $query->where('is_available', $isAvailable);
        }


        $products = $query->latest()->paginate(10);

        $products->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'photo' => $item->photo,
                'category_name' => $item->category->name ?? '-',
                'option_name' => $item->option->name ?? '-',
                'is_available' => $item->is_available,
            ];
        });

        return response()->json($products);
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $options = Option::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'product' => $product,
            'categories' => $categories,
            'options' => $options
        ]);
    }

    public function update(Request $request, $id)
    { 
        $product = Product::findOrFail($id);
    
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'option_id' => 'nullable|exists:options,id',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        
        if ($request->hasFile('photo')) {
            if ($product->photo && Storage::disk('public')->exists('product/' . $product->photo)) {
                Storage::disk('public')->delete('product/' . $product->photo);
            }
            
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('product', $filename, 'public');
            $validated['photo'] = $filename;
        }
        
        $product->update($validated);
        
        return response()->json([
            'message' => 'Product berhasil diperbarui',
            'data' => $product
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'description' => 'nullable|string',  
            'option_id' => 'nullable|exists:options,id',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', 
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('product', $filename, 'public');
            $validated['photo'] = $filename;
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    public function masterFormData()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $options = Option::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'categories' => $categories,
            'options' => $options
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }

        if ($product->photo && Storage::disk('public')->exists('product/' . $product->photo)) {
            Storage::disk('public')->delete('product/' . $product->photo);
        }

        $product->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus.'
        ], 200);
    }
}
