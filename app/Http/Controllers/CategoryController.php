<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function getData(Request $request){
            $search = $request->search;
            $query = Category::query();
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            $data = $query->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', 
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Categori berhasil ditambahkan',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->only(['name','description']));
        return response()->json(['message' => 'Category berhasil diperbarui']);
    }
    
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category tidak ditemukan.'
            ], 404);
        }

        if ($category->product()->count() > 0) {
            return response()->json([
                'message' => 'Category ini tidak bisa dihapus karena sedang digunakan oleh menu/produk.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category berhasil dihapus.'
        ], 200);
    }
}
