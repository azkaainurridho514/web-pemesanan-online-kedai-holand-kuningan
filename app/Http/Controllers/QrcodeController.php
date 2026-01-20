<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Qrcode as QR;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class QrcodeController extends Controller
{
    public function index(){
        return view("dashboard.qr.index");
    }

    public function datas(Request $request){
        $search = $request->search;
        $query = QR::query();
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
            'desc' => 'nullable|string',
        ]);
        $data = QR::create($validated);

        $fileName = time() . '.svg';
        $qrImage = QrCode::format('svg')->size(300)->generate($data->id); 

        Storage::put('public/qr/' . $fileName, $qrImage);

        $data->update([
            'path' => $fileName
        ]);

        return response()->json([
            'message' => 'Qrcode berhasil ditambahkan',
            'data' => $data
        ], 201);
    }
    public function show($id)
    {
        $qr = QR::where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR Code tidak ditemukan atau tidak aktif'
            ], 404);
        }

        $orderAktif = Order::where('qr_code_id', $qr->id)
            ->whereNotIn('status', ['selesai', 'batal'])
            ->exists();

        if ($orderAktif) {
            return response()->json([
                'message' => 'Meja sedang di gunakan, pilih meja lain',
                'status' => 'used'
            ], 200); 
        }

        return response()->json([
            'message' => 'QR Code valid dan dapat digunakan',
            'status' => 'valid',
            'data' => $qr
        ]);
    }


    // old ====================
    // public function show($id)
    // {
    //     $qr = QR::findOrFail($id)->where('is_active', true)->first();
    //     if (!$qr) {
    //         return response()->json([
    //             'message' => 'QR Code tidak ditemukan atau tidak aktif'
    //         ], 404);
    //     }
    //     return response()->json($qr);
    // }

    public function update(Request $request, $id)
    {
        $qr = QR::findOrFail($id);
        $qr->update($request->only(['name','desc', 'is_active']));
        return response()->json(['message' => 'Qrcode berhasil diperbarui']);
    }
    
    public function destroy($id)
    {
        $qr = QR::find($id);

        if (!$qr) {
            return response()->json([
                'message' => 'Qrcode tidak ditemukan.'
            ], 404);
        }

        $qr->delete();

        return response()->json([
            'message' => 'Qrcode berhasil dihapus.'
        ], 200);
    }
}
