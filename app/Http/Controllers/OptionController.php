<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\OptionItems;


class OptionController extends Controller
{
    public function getDataOption(Request $request)
    {
        $search = $request->input('search');

        $options = Option::select('id', 'name')
            ->withCount('items')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar option berhasil diambil.',
            'data' => $options,
        ]);
    }

    public function getDataOptionDetail($id)
    {
        
        $option = Option::with('items')->find($id);

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Option tidak ditemukan.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Detail option berhasil diambil.',
            'data' => $option,
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'array',
            'items.*.id' => 'nullable|exists:option_items,id',
            'items.*.name' => 'required|string|max:255',
        ]);

        $option = Option::find($id);
        if (!$option) {
            return response()->json(['message' => 'Option tidak ditemukan'], 404);
        }

        $option->name = $request->name;
        $option->save();

        $existingItemIds = $option->items()->pluck('id')->toArray();
        $submittedItemIds = [];

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (isset($item['id']) && in_array($item['id'], $existingItemIds)) {
                    $optItem = OptionItems::find($item['id']);
                    $optItem->name = $item['name'];
                    $optItem->save();
                    $submittedItemIds[] = $optItem->id;
                } else {
                    $optItem = new OptionItems();
                    $optItem->option_id = $option->id;
                    $optItem->name = $item['name'];
                    $optItem->save();
                    $submittedItemIds[] = $optItem->id;
                }
            }
        }

        $option->items()->whereNotIn('id', $submittedItemIds)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option dan item berhasil diperbarui'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
        ]);

        $option = Option::create([
            'name' => $request->name
        ]);

        foreach ($request->items as $item) {
            OptionItems::create([
                'option_id' => $option->id,
                'name' => $item['name']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Option dan item berhasil ditambahkan',
            'data' => [
                'id' => $option->id,
                'name' => $option->name
            ]
        ]);
    }

    public function destroy($id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json([
                'message' => 'Option tidak ditemukan.'
            ], 404);
        }

        if ($option->product()->count() > 0) {
            return response()->json([
                'message' => 'Option ini tidak bisa dihapus karena sedang digunakan oleh menu/produk.'
            ], 400);
        }

        $option->items()->delete();

        $option->delete();

        return response()->json([
            'message' => 'Option berhasil dihapus.'
        ], 200);
    }
}
